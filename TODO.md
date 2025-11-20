# TODO: Implement Top Up Saldo System

## 1. Create Database Table
- [ ] Execute SQL to create `topup_history` table with foreign key to users.

## 2. Create Topup Folder and Files
- [ ] Create `Topup/` folder in root.
- [ ] Create `Topup/topup.php`: Top-up page with balance display, amount options, payment methods, AJAX form.
- [ ] Create `Topup/topup.css`: Styling matching website theme.
- [ ] Create `Topup/topup.js`: Client-side logic for form, AJAX to process_topup.php, SweetAlert.
- [ ] Create `Topup/process_topup.php`: Handle AJAX request: insert topup_history, simulate success, update saldo.

## 3. Update Dashboard
- [ ] Add "Topup History" tab after Inventory in dashboard.php.
- [ ] Add tab content: table displaying topup_history with username, amount, method, status, date.
- [ ] Add actions: Approve (set status to success, update saldo), Delete.
- [ ] Create PHP logic for approve/delete actions (AJAX or direct).

## 4. Update Index.php
- [ ] Change balance widget link from "topup.php" to "Topup/topup.php".

## 5. Testing
- [ ] Test top-up flow: select amount, method, submit, check saldo update, SweetAlert.
- [ ] Test admin approve: change status, verify saldo update.
- [ ] Test delete in admin.
