<?php

use Illuminate\Support\Facades\Route;

// =============================================
// Dashboard
// =============================================
Route::get('/', function () {
    return view('pages.dashboard.index', ['title' => 'Dashboard']);
})->name('dashboard');

// =============================================
// Order Management
// =============================================
Route::prefix('orders')->group(function () {
    Route::get('/all', fn() => view('pages.orders.all', ['title' => 'All Orders']))->name('orders.all');
    Route::get('/create', fn() => view('pages.orders.create', ['title' => 'Create Order']))->name('orders.create');
    Route::get('/merchant', fn() => view('pages.orders.merchant', ['title' => 'Merchant Orders']))->name('orders.merchant');
    Route::get('/payment-mismatch', fn() => view('pages.orders.payment-mismatch', ['title' => 'Payment Mismatch Monitoring']))->name('orders.payment-mismatch');
    Route::get('/no-activity', fn() => view('pages.orders.no-activity', ['title' => 'No Activity Orders']))->name('orders.no-activity');
    Route::get('/spam', fn() => view('pages.orders.spam', ['title' => 'Spam Orders']))->name('orders.spam');
    Route::get('/refunds', fn() => view('pages.orders.refunds', ['title' => 'Refund Requests']))->name('orders.refunds');
    Route::get('/returns', fn() => view('pages.orders.returns', ['title' => 'Return Orders']))->name('orders.returns');
    Route::get('/{id}', fn() => view('pages.orders.detail', ['title' => 'Order Details']))->name('orders.detail');
});

// =============================================
// Product Management
// =============================================
Route::prefix('products')->group(function () {
    Route::get('/dashboard', fn() => view('pages.products.dashboard', ['title' => 'Products Dashboard']))->name('products.dashboard');
    Route::get('/all', fn() => view('pages.products.all', ['title' => 'All Products']))->name('products.all');
    Route::get('/live', fn() => view('pages.products.live', ['title' => 'Live Products']))->name('products.live');
    Route::get('/pending', fn() => view('pages.products.pending', ['title' => 'Approval Pending']))->name('products.pending');
    Route::get('/update-requests', fn() => view('pages.products.update-requests', ['title' => 'Update Requests']))->name('products.update-requests');
    Route::get('/categories', fn() => view('pages.products.categories', ['title' => 'Categories']))->name('products.categories');
    Route::get('/brands', fn() => view('pages.products.brands', ['title' => 'Brands']))->name('products.brands');
});

// =============================================
// Merchants
// =============================================
Route::prefix('merchants')->group(function () {
    Route::get('/all', fn() => view('pages.merchants.all', ['title' => 'All Merchants']))->name('merchants.all');
    Route::get('/online', fn() => view('pages.merchants.online', ['title' => 'Online Merchants']))->name('merchants.online');
    Route::get('/offline', fn() => view('pages.merchants.offline', ['title' => 'Offline Merchants']))->name('merchants.offline');
    Route::get('/{id}', fn() => view('pages.merchants.detail', ['title' => 'Merchant Details']))->name('merchants.detail');
});

// =============================================
// Accounts (Finance)
// =============================================
Route::prefix('accounts')->group(function () {
    Route::get('/dashboard', fn() => view('pages.accounts.dashboard', ['title' => 'Accounts Dashboard']))->name('accounts.dashboard');
    Route::get('/payouts', fn() => view('pages.accounts.payouts', ['title' => 'Payout Requests']))->name('accounts.payouts');
    Route::get('/payouts/{id}', fn() => view('pages.accounts.payout-details', ['title' => 'Payout Details']))->name('accounts.payout-details');
    Route::get('/today-payments', fn() => view('pages.accounts.today-payments', ['title' => "Today's Payments"]))->name('accounts.today-payments');
    Route::get('/payables', fn() => view('pages.accounts.payables', ['title' => 'Payables']))->name('accounts.payables');
    Route::get('/ssl-payments', fn() => view('pages.accounts.ssl-payments', ['title' => 'SSL Payments']))->name('accounts.ssl-payments');
    Route::get('/merchant-balances', fn() => view('pages.accounts.merchant-balances', ['title' => 'Merchant Balances']))->name('accounts.merchant-balances');
    Route::get('/sfc-payments', fn() => view('pages.accounts.sfc-payments', ['title' => 'SFC Payments']))->name('accounts.sfc-payments');
});

// =============================================
// Marketing & Growth
// =============================================
Route::prefix('marketing')->group(function () {
    Route::get('/dashboard', fn() => view('pages.marketing.dashboard', ['title' => 'Marketing Dashboard']))->name('marketing.dashboard');
    Route::get('/campaigns', fn() => view('pages.marketing.campaigns', ['title' => 'Campaigns']))->name('marketing.campaigns');
    Route::get('/campaigns/{id}', fn() => view('pages.marketing.campaign-detail', ['title' => 'Campaign Details']))->name('marketing.campaign-detail');
    Route::get('/prime-views', fn() => view('pages.marketing.prime-views', ['title' => 'Prime Views']))->name('marketing.prime-views');
    Route::get('/analytics', fn() => view('pages.marketing.analytics', ['title' => 'Analytics']))->name('marketing.analytics');
    Route::get('/visitors', fn() => view('pages.marketing.visitors', ['title' => 'Visitors']))->name('marketing.visitors');
    Route::get('/coupons', fn() => view('pages.marketing.coupons', ['title' => 'Coupons']))->name('marketing.coupons');
    Route::get('/badges', fn() => view('pages.marketing.badges', ['title' => 'Badges']))->name('marketing.badges');
});

// =============================================
// Shop & Content
// =============================================
Route::prefix('shop')->group(function () {
    Route::get('/featured', fn() => view('pages.shop.featured', ['title' => 'Featured Shops']))->name('shop.featured');
    Route::get('/sliders', fn() => view('pages.shop.sliders', ['title' => 'Sliders']))->name('shop.sliders');
    Route::get('/reels', fn() => view('pages.shop.reels', ['title' => 'Reels']))->name('shop.reels');
    Route::get('/pages', fn() => view('pages.shop.pages', ['title' => 'Pages']))->name('shop.pages');
    Route::get('/sell-with-us', fn() => view('pages.shop.sell-with-us', ['title' => 'Sell With Us']))->name('shop.sell-with-us');
    Route::get('/faq', fn() => view('pages.shop.faq', ['title' => 'FAQ']))->name('shop.faq');
});

// =============================================
// Operations
// =============================================
Route::prefix('operations')->group(function () {
    Route::get('/locations', fn() => view('pages.operations.locations', ['title' => 'Locations']))->name('operations.locations');
    Route::get('/return-reasons', fn() => view('pages.operations.return-reasons', ['title' => 'Return Reasons']))->name('operations.return-reasons');
});

// =============================================
// Feedback & Support
// =============================================
Route::prefix('feedback')->group(function () {
    Route::get('/order-reviews', fn() => view('pages.feedback.order-reviews', ['title' => 'Order Reviews']))->name('feedback.order-reviews');
    Route::get('/product-questions', fn() => view('pages.feedback.product-questions', ['title' => 'Product Questions']))->name('feedback.product-questions');
    Route::get('/help-requests', fn() => view('pages.feedback.help-requests', ['title' => 'Help Requests']))->name('feedback.help-requests');
    Route::get('/merchant-issues', fn() => view('pages.feedback.merchant-issues', ['title' => 'Merchant Issues']))->name('feedback.merchant-issues');
    Route::get('/category-requests', fn() => view('pages.feedback.category-requests', ['title' => 'Category Requests']))->name('feedback.category-requests');
    Route::get('/shop-update-requests', fn() => view('pages.feedback.shop-update-requests', ['title' => 'Shop Update Requests']))->name('feedback.shop-update-requests');
});

// =============================================
// Settings
// =============================================
Route::prefix('settings')->group(function () {
    Route::get('/general', fn() => view('pages.settings.general', ['title' => 'General Settings']))->name('settings.general');
    Route::get('/application', fn() => view('pages.settings.application', ['title' => 'Application Settings']))->name('settings.application');
    Route::get('/system', fn() => view('pages.settings.system', ['title' => 'System Configuration']))->name('settings.system');
    Route::get('/payment-methods', fn() => view('pages.settings.payment-methods', ['title' => 'Payment Methods']))->name('settings.payment-methods');
});

// =============================================
// User Management
// =============================================
Route::prefix('users')->group(function () {
    Route::get('/admins', fn() => view('pages.users.admins', ['title' => 'Admins']))->name('users.admins');
    Route::get('/roles', fn() => view('pages.users.roles', ['title' => 'Roles']))->name('users.roles');
});

// =============================================
// Merchants (extended)
// =============================================
Route::get('/merchants/update-requests',  fn() => view('pages.merchants.update-requests',  ['title' => 'Merchant Update Requests']))->name('merchants.update-requests');
Route::get('/merchants/onboarding',       fn() => view('pages.merchants.onboarding',        ['title' => 'Merchant Onboarding']))->name('merchants.onboarding');
Route::get('/merchants/kam-dashboard',    fn() => view('pages.merchants.kam-dashboard',     ['title' => 'KAM Dashboard']))->name('merchants.kam-dashboard');
Route::get('/merchants/deactivation-log', fn() => view('pages.merchants.deactivation-log',  ['title' => 'Deactivation Log']))->name('merchants.deactivation-log');

// =============================================
// Accounts (extended)
// =============================================
Route::get('/accounts/adjustments', fn() => view('pages.accounts.adjustments', ['title' => 'Amount Adjustments']))->name('accounts.adjustments');

// =============================================
// Analytics
// =============================================
Route::prefix('analytics')->group(function () {
    Route::get('/dashboard',     fn() => view('pages.analytics.dashboard',     ['title' => 'Analytics Dashboard']))->name('analytics.dashboard');
    Route::get('/agent-metrics', fn() => view('pages.analytics.agent-metrics', ['title' => 'Agent Metrics']))->name('analytics.agent-metrics');
});

// =============================================
// SLA & Performance
// =============================================
Route::prefix('sla')->group(function () {
    Route::get('/dashboard', fn() => view('pages.sla.dashboard', ['title' => 'SLA Dashboard']))->name('sla.dashboard');
    Route::get('/rules',     fn() => view('pages.sla.rules',     ['title' => 'SLA Rules']))->name('sla.rules');
});

// =============================================
// Tools
// =============================================
Route::prefix('tools')->group(function () {
    Route::get('/phone-lookup', fn() => view('pages.tools.phone-lookup', ['title' => 'Phone Lookup']))->name('tools.phone-lookup');
});

// =============================================
// Settings (extended)
// =============================================
Route::get('/settings/call-configurator', fn() => view('pages.settings.call-configurator', ['title' => 'Call Configurator']))->name('settings.call-configurator');

// =============================================
// Customers
// =============================================
Route::prefix('customers')->group(function () {
    Route::get('/all', fn() => view('pages.customers.all', ['title' => 'All Customers']))->name('customers.all');
    Route::get('/blocked', fn() => view('pages.customers.blocked', ['title' => 'Blocked Customers']))->name('customers.blocked');
    Route::get('/{id}', fn() => view('pages.customers.detail', ['title' => 'Customer Details']))->name('customers.detail');
});

// =============================================
// File Manager
// =============================================
Route::get('/file-manager', fn() => view('pages.file-manager.index', ['title' => 'File Manager']))->name('file-manager');

// =============================================
// Reference Pages (from template)
// =============================================
Route::get('/calendar', fn() => view('pages.calender', ['title' => 'Calendar']))->name('calendar');
Route::get('/profile', fn() => view('pages.profile', ['title' => 'Profile']))->name('profile');
Route::get('/form-elements', fn() => view('pages.form.form-elements', ['title' => 'Form Elements']))->name('form-elements');
Route::get('/basic-tables', fn() => view('pages.tables.basic-tables', ['title' => 'Basic Tables']))->name('basic-tables');
Route::get('/blank', fn() => view('pages.blank', ['title' => 'Blank']))->name('blank');
Route::get('/error-404', fn() => view('pages.errors.error-404', ['title' => 'Error 404']))->name('error-404');
Route::get('/line-chart', fn() => view('pages.chart.line-chart', ['title' => 'Line Chart']))->name('line-chart');
Route::get('/bar-chart', fn() => view('pages.chart.bar-chart', ['title' => 'Bar Chart']))->name('bar-chart');
Route::get('/signin', fn() => view('pages.auth.signin', ['title' => 'Sign In']))->name('signin');
Route::get('/signup', fn() => view('pages.auth.signup', ['title' => 'Sign Up']))->name('signup');
Route::get('/alerts', fn() => view('pages.ui-elements.alerts', ['title' => 'Alerts']))->name('alerts');
Route::get('/avatars', fn() => view('pages.ui-elements.avatars', ['title' => 'Avatars']))->name('avatars');
Route::get('/badge', fn() => view('pages.ui-elements.badges', ['title' => 'Badges']))->name('badges');
Route::get('/buttons', fn() => view('pages.ui-elements.buttons', ['title' => 'Buttons']))->name('buttons');
Route::get('/image', fn() => view('pages.ui-elements.images', ['title' => 'Images']))->name('images');
Route::get('/videos', fn() => view('pages.ui-elements.videos', ['title' => 'Videos']))->name('videos');
