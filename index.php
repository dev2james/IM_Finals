<?php
require __DIR__ . '/bootstrap.php';

$pages = ['dashboard', 'allowance', 'expenses', 'categories', 'budget', 'savings', 'reports', 'notifications', 'profile'];
$page = in_array($_GET['page'] ?? '', $pages, true) ? $_GET['page'] : 'dashboard';
$models = ['allowance' => Allowance::class, 'expense' => Expense::class, 'category' => Category::class, 'saving' => Saving::class];
$back = ['allowance' => 'allowance', 'expense' => 'expenses', 'category' => 'categories', 'saving' => 'savings'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf(), $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Invalid request.');
    }
    $ent = $_POST['entity'] ?? '';
    $dest = 'index.php?page=' . ($back[$ent] ?? $page);
    try {
        switch ($_POST['action'] ?? '') {
            case 'save':
                if (!isset($models[$ent]))
                    throw new InvalidArgumentException('Unknown record type.');
                $m = new $models[$ent];
                $d = $_POST;
                $d['user_id'] = USER_ID;
                if ($err = $m->validate($d))
                    throw new InvalidArgumentException($err);
                $id = (int) ($_POST['id'] ?? 0);
                Database::transaction(function () use ($m, $d, $id): void {
                    $id ? $m->update($id, $d) : $m->create($d);
                });
                flash('Saved.');
                break;
            case 'delete':
                if (!isset($models[$ent]))
                    throw new InvalidArgumentException('Unknown record type.');
                $m = new $models[$ent];
                $id = (int) $_POST['id'];
                Database::transaction(function () use ($m, $id): void {
                    $m->delete($id);
                });
                flash('Deleted.');
                break;
            case 'budget':
                $dest = 'index.php?page=budget';
                $b = new Budget;
                if ($err = $b->validate($_POST))
                    throw new InvalidArgumentException($err);
                $limit = (float) $_POST['monthly_limit'];
                Database::transaction(function () use ($b, $limit): void {
                    $b->setLimit($limit);
                });
                flash('Budget saved.');
                break;
            case 'profile':
                $dest = 'index.php?page=profile';
                $u = new User;
                if ($err = $u->validate($_POST))
                    throw new InvalidArgumentException($err);
                $oldImage = $u->profile()['profile_image'] ?? null;
                $newImage = $u->saveProfileImage($_FILES['profile_image'] ?? []);
                try {
                    $u->updateProfile($_POST, $newImage);
                } catch (Throwable $x) {
                    if ($newImage !== null) {
                        $u->removeProfileImage($newImage);
                    }
                    throw $x;
                }
                if ($newImage !== null) {
                    $u->removeProfileImage($oldImage);
                }
                flash('Profile saved.');
                break;
            case 'report':
                $r = new Report;
                if ($err = $r->validate($_POST))
                    throw new InvalidArgumentException($err);
                $r->log($_POST['report_type']);
                $dest = 'index.php?page=reports&type=' . urlencode($_POST['report_type']);
                break;
        }
    } catch (InvalidArgumentException | RuntimeException $x) {
        flash($x->getMessage(), 'error');
    } catch (PDOException $x) {
        flash('Database error. Check config.php and that schema.sql was imported.', 'error');
    }
    redirect($dest);
}

/* ---------- Prepare data for the page ---------- */
try {
    $allowance = new Allowance;
    $expense = new Expense;
    $category = new Category;
    $budget = new Budget;
    $saving = new Saving;
    $report = new Report;
    $user = new User;
    $notifier = new Notifier($budget, $allowance, $expense, $saving);

    $profile = $user->profile();
    $badge = $notifier->alertCount();
    $edit = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;

    switch ($page) {
        case 'dashboard':
            $al = $allowance->total();
            $ex = $expense->total();
            $sv = $saving->total();
            $data = [
                'al' => $al,
                'ex' => $ex,
                'sv' => $sv,
                'bal' => $al - $ex - $sv,
                'b' => $budget->current(),
                'cats' => $expense->byCategory(),
                'recent' => array_slice($expense->search(), 0, 6)
            ];
            break;
        case 'allowance':
            $data = ['rows' => $allowance->all(), 'row' => $edit ? $allowance->find($edit) : null];
            break;
        case 'expenses':
            $f = ['q' => trim($_GET['q'] ?? ''), 'c' => (int) ($_GET['c'] ?? 0), 'from' => $_GET['from'] ?? '', 'to' => $_GET['to'] ?? ''];
            $data = [
                'rows' => $expense->search($f['q'], $f['c'], $f['from'], $f['to']),
                'f' => $f,
                'cats' => $category->all(),
                'row' => $edit ? $expense->find($edit) : null
            ];
            break;
        case 'categories':
            $data = ['rows' => $category->all(), 'row' => $edit ? $category->find($edit) : null, 'counter' => $category];
            break;
        case 'budget':
            $data = ['b' => $budget->current(), 'cats' => $expense->byCategory(date('Y-m'))];
            break;
        case 'savings':
            $data = ['rows' => $saving->all(), 'goals' => $saving->byGoal(), 'row' => $edit ? $saving->find($edit) : null];
            break;
        case 'reports':
            $type = $_GET['type'] ?? '';
            $data = ['history' => $report->history(), 'type' => $type, 'result' => in_array($type, Report::TYPES, true) ? $report->build($type) : null];
            break;
        case 'notifications':
            $data = ['notes' => $notifier->all()];
            break;
        default:
            $data = [];
    }
    $data += ['page' => $page, 'profile' => $profile, 'badge' => $badge];
    View::render($page, $data);
} catch (PDOException $x) {
    http_response_code(500);
    error_log('BudgetWise database error: ' . $x->getMessage());
    echo '<p style="font-family:sans-serif;padding:2rem">Cannot connect to the database. Check the database host, name, username, and password in <b>config.local.php</b>, and make sure <b>schema.sql</b> has been imported.</p>';
}
