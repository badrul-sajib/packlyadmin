<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Main',
                'items' => [
                    [
                        'icon' => 'dashboard',
                        'name' => 'Dashboard',
                        'path' => '/',
                    ],
                ],
            ],
            [
                'title' => 'Order Management',
                'items' => [
                    [
                        'icon' => 'orders',
                        'name' => 'Order Management',
                        'subItems' => [
                            ['name' => 'All Orders', 'path' => '/orders/all', 'desc' => 'Pending, processing, delivered, cancelled'],
                            ['name' => 'Merchant Orders', 'path' => '/orders/merchant', 'desc' => 'Orders grouped by merchants'],
                            ['name' => 'Payment Mismatch', 'path' => '/orders/payment-mismatch', 'desc' => 'COD & shipping mismatches'],
                            ['name' => 'No Activity Orders', 'path' => '/orders/no-activity', 'desc' => 'Orders with no updates for a long time'],
                            ['name' => 'Spam Orders', 'path' => '/orders/spam', 'desc' => 'Suspicious or fake orders'],
                            ['name' => 'Refund Requests', 'path' => '/orders/refunds', 'desc' => 'Customer refund requests'],
                            ['name' => 'Return Orders', 'path' => '/orders/returns', 'desc' => 'Returned or cancelled orders'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Product Management',
                'items' => [
                    [
                        'icon' => 'products',
                        'name' => 'Product Management',
                        'subItems' => [
                            ['name' => 'Dashboard', 'path' => '/products/dashboard', 'desc' => 'Product overview & stats'],
                            ['name' => 'All Products', 'path' => '/products/all', 'desc' => 'All requested & approved products'],
                            ['name' => 'Live Products', 'path' => '/products/live', 'desc' => 'Currently visible on the site'],
                            ['name' => 'Approval Pending', 'path' => '/products/pending', 'desc' => 'New products waiting for approval'],
                            ['name' => 'Update Requests', 'path' => '/products/update-requests', 'desc' => 'Price, title changes pending approval'],
                            ['name' => 'Categories', 'path' => '/products/categories', 'desc' => 'Manage product categories'],
                            ['name' => 'Brands', 'path' => '/products/brands', 'desc' => 'Manage product brands'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'User Management',
                'items' => [
                    [
                        'icon' => 'merchants',
                        'name' => 'Merchants',
                        'subItems' => [
                            ['name' => 'All Merchants',     'path' => '/merchants/all',            'desc' => 'All registered merchants'],
                            ['name' => 'Online Merchants',  'path' => '/merchants/online',          'desc' => 'Currently active merchants'],
                            ['name' => 'Offline Merchants', 'path' => '/merchants/offline',         'desc' => 'Inactive or disabled merchants'],
                            ['name' => 'Update Requests',   'path' => '/merchants/update-requests', 'desc' => 'Pending shop profile change approvals'],
                            ['name' => 'Onboarding',        'path' => '/merchants/onboarding',      'desc' => 'Merchant activation pipeline'],
                            ['name' => 'KAM Dashboard',     'path' => '/merchants/kam-dashboard',   'desc' => 'KAM agent performance & assignments'],
                            ['name' => 'Deactivation Log',  'path' => '/merchants/deactivation-log','desc' => 'Historical merchant deactivation records'],
                        ],
                    ],
                    [
                        'icon' => 'customers',
                        'name' => 'Customers',
                        'subItems' => [
                            ['name' => 'All Customers', 'path' => '/customers/all', 'desc' => 'All registered customers'],
                            ['name' => 'Blocked Customers', 'path' => '/customers/blocked', 'desc' => 'Suspended or banned accounts'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Accounts',
                'items' => [
                    [
                        'icon' => 'finance',
                        'name' => 'Accounts (Finance)',
                        'subItems' => [
                            ['name' => 'Dashboard', 'path' => '/accounts/dashboard', 'desc' => 'Financial overview & summaries'],
                            ['name' => 'Payout Requests', 'path' => '/accounts/payouts', 'desc' => 'Merchant withdrawal requests'],
                            ['name' => "Today's Payments", 'path' => '/accounts/today-payments', 'desc' => 'Payments processed today'],
                            ['name' => 'Payables', 'path' => '/accounts/payables', 'desc' => 'Amounts owed to merchants'],
                            ['name' => 'SSL Payments', 'path' => '/accounts/ssl-payments', 'desc' => 'Via SSL gateway'],
                            ['name' => 'Merchant Balances', 'path' => '/accounts/merchant-balances', 'desc' => 'Current balances of merchants'],
                            ['name' => 'SFC Payments', 'path' => '/accounts/sfc-payments', 'desc' => 'Via SFC/manual system'],
                            ['name' => 'Adjustments',  'path' => '/accounts/adjustments',  'desc' => 'Manual credit & debit adjustments'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Marketing & Growth',
                'items' => [
                    [
                        'icon' => 'marketing',
                        'name' => 'Marketing & Growth',
                        'subItems' => [
                            ['name' => 'Dashboard', 'path' => '/marketing/dashboard', 'desc' => 'Campaign & performance overview'],
                            ['name' => 'Campaigns', 'path' => '/marketing/campaigns', 'desc' => 'Create & manage campaigns'],
                            ['name' => 'Prime Views', 'path' => '/marketing/prime-views', 'desc' => 'Featured product placements'],
                            ['name' => 'Analytics', 'path' => '/marketing/analytics', 'desc' => 'Platform performance metrics'],
                            ['name' => 'Visitors', 'path' => '/marketing/visitors', 'desc' => 'Traffic & visitor insights'],
                            ['name' => 'Coupons', 'path' => '/marketing/coupons', 'desc' => 'Discount codes management'],
                            ['name' => 'Badges', 'path' => '/marketing/badges', 'desc' => 'Promotional labels (Hot, Sale, New)'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Shop & Content',
                'items' => [
                    [
                        'icon' => 'shop',
                        'name' => 'Shop & Content',
                        'subItems' => [
                            ['name' => 'Featured Shops', 'path' => '/shop/featured', 'desc' => 'Highlighted shops on homepage'],
                            ['name' => 'Sliders', 'path' => '/shop/sliders', 'desc' => 'Homepage banners & sliders'],
                            ['name' => 'Reels', 'path' => '/shop/reels', 'desc' => 'Short video content for promotion'],
                            ['name' => 'Pages', 'path' => '/shop/pages', 'desc' => 'About, Terms, Privacy pages'],
                            ['name' => 'Sell With Us', 'path' => '/shop/sell-with-us', 'desc' => 'Merchant onboarding content'],
                            ['name' => 'FAQ', 'path' => '/shop/faq', 'desc' => 'Frequently asked questions'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Operations',
                'items' => [
                    [
                        'icon' => 'operations',
                        'name' => 'Operations',
                        'subItems' => [
                            ['name' => 'Locations', 'path' => '/operations/locations', 'desc' => 'Manage delivery/service areas'],
                            ['name' => 'Return Reasons', 'path' => '/operations/return-reasons', 'desc' => 'Returns, cancellations, issues'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Feedback & Support',
                'items' => [
                    [
                        'icon' => 'feedback',
                        'name' => 'Feedback & Support',
                        'subItems' => [
                            ['name' => 'Order Reviews', 'path' => '/feedback/order-reviews', 'desc' => 'Customer reviews on orders'],
                            ['name' => 'Product Questions', 'path' => '/feedback/product-questions', 'desc' => 'Questions asked by customers'],
                            ['name' => 'Help Requests', 'path' => '/feedback/help-requests', 'desc' => 'General support tickets'],
                            ['name' => 'Merchant Issues', 'path' => '/feedback/merchant-issues', 'desc' => 'Problems reported by merchants'],
                            ['name' => 'Category Requests', 'path' => '/feedback/category-requests', 'desc' => 'Requests to add new categories'],
                            ['name' => 'Shop Update Requests', 'path' => '/feedback/shop-update-requests', 'desc' => 'Requests to update shop info'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Settings',
                'items' => [
                    [
                        'icon' => 'settings',
                        'name' => 'Settings',
                        'subItems' => [
                            ['name' => 'General Settings', 'path' => '/settings/general', 'desc' => 'Website info, branding, config'],
                            ['name' => 'Application Settings', 'path' => '/settings/application', 'desc' => 'App behavior & system preferences'],
                            ['name' => 'System Configuration', 'path' => '/settings/system', 'desc' => 'Advanced system-level controls'],
                            ['name' => 'Payment Methods',   'path' => '/settings/payment-methods',  'desc' => 'Configure payment gateways'],
                            ['name' => 'Call Configurator', 'path' => '/settings/call-configurator', 'desc' => 'Call center & communication settings'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Administration',
                'items' => [
                    [
                        'icon' => 'users',
                        'name' => 'User Management',
                        'subItems' => [
                            ['name' => 'Admins', 'path' => '/users/admins', 'desc' => 'Manage admin users'],
                            ['name' => 'Roles', 'path' => '/users/roles', 'desc' => 'Permissions & access control'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Analytics',
                'items' => [
                    [
                        'icon' => 'analytics',
                        'name' => 'Analytics',
                        'subItems' => [
                            ['name' => 'Dashboard',     'path' => '/analytics/dashboard',     'desc' => 'Core business analytics overview'],
                            ['name' => 'Agent Metrics', 'path' => '/analytics/agent-metrics', 'desc' => 'KAM cancel & delivery performance'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'SLA & Performance',
                'items' => [
                    [
                        'icon' => 'sla',
                        'name' => 'SLA & Performance',
                        'subItems' => [
                            ['name' => 'SLA Dashboard', 'path' => '/sla/dashboard', 'desc' => 'Ticket resolution times & breach rates'],
                            ['name' => 'SLA Rules',     'path' => '/sla/rules',     'desc' => 'Define SLA targets per category'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Tools',
                'items' => [
                    [
                        'icon' => 'tools',
                        'name' => 'Tools',
                        'subItems' => [
                            ['name' => 'Phone Lookup', 'path' => '/tools/phone-lookup', 'desc' => 'Detect duplicate or suspicious phone numbers'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Media',
                'items' => [
                    [
                        'icon' => 'file-manager',
                        'name' => 'File Manager',
                        'path' => '/file-manager',
                    ],
                ],
            ],
        ];
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/'));
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"></path></svg>',

            'orders' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5M12 12H15M12 16H15M9 12H9.01M9 16H9.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'products' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 7L12 3L4 7M20 7L12 11M20 7V17L12 21M12 11L4 7M12 11V21M4 7V17L12 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'merchants' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 21H21M3 10H21M5 10V21M19 10V21M4 10L6.5 3H17.5L20 10M9 14H15V21H9V14Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'finance' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 6V18M9 15.182C9.513 15.694 10.205 16 11 16H13.21C14.199 16 15 15.164 15 14.136C15 13.108 14.199 12.272 13.21 12.272H10.79C9.801 12.272 9 11.437 9 10.409C9 9.38 9.801 8.545 10.79 8.545H13C13.795 8.545 14.487 8.85 15 9.363M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'marketing' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 5.882V19.24C11 19.7667 10.5667 20 10.24 20H9.88C9.39 20 8.92 19.81 8.56 19.47L5.14 16.18C4.5 15.56 4.5 14.56 5.14 13.94L6 13.11M11 5.882C11.2 4.834 12.168 4 13.24 4H18.76C19.832 4 20.8 4.834 21 5.882V5.882C21.2 6.93 20.232 8 19.16 8H13.64C12.568 8 11.2 6.93 11 5.882V5.882ZM6 13.11L3.5 17.5M6 13.11L8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'shop' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 9L5 3H19L21 9M3 9H21M3 9V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V9M12 9V3M7.5 9C7.5 10.3807 6.38071 11.5 5 11.5C3.61929 11.5 3 10.3807 3 9M12 9C12 10.3807 10.8807 11.5 9.5 11.5C8.11929 11.5 7.5 10.3807 7.5 9M16.5 9C16.5 10.3807 15.3807 11.5 14 11.5C12.6193 11.5 12 10.3807 12 9M21 9C21 10.3807 20.3807 11.5 19 11.5C17.6193 11.5 16.5 10.3807 16.5 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'operations' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.325 4.317C10.751 2.561 13.249 2.561 13.675 4.317C13.7389 4.5808 13.8642 4.82578 14.0407 5.032C14.2172 5.23822 14.4399 5.39985 14.6907 5.50375C14.9414 5.60764 15.2132 5.65085 15.4838 5.62987C15.7544 5.60889 16.0162 5.5243 16.248 5.383C17.791 4.443 19.558 6.209 18.618 7.753C18.4769 7.98466 18.3924 8.24634 18.3715 8.51677C18.3506 8.7872 18.3938 9.05877 18.4975 9.30938C18.6013 9.55999 18.7627 9.78258 18.9687 9.95905C19.1747 10.1355 19.4194 10.2609 19.683 10.325C21.439 10.751 21.439 13.249 19.683 13.675C19.4192 13.7389 19.1742 13.8642 18.968 14.0407C18.7618 14.2172 18.6002 14.4399 18.4963 14.6907C18.3924 14.9414 18.3491 15.2132 18.3701 15.4838C18.3911 15.7544 18.4757 16.0162 18.617 16.248C19.557 17.791 17.791 19.558 16.247 18.618C16.0153 18.4769 15.7537 18.3924 15.4832 18.3715C15.2128 18.3506 14.9412 18.3938 14.6906 18.4975C14.44 18.6013 14.2174 18.7627 14.0409 18.9687C13.8645 19.1747 13.7391 19.4194 13.675 19.683C13.249 21.439 10.751 21.439 10.325 19.683C10.2611 19.4192 10.1358 19.1742 9.95929 18.968C9.7828 18.7618 9.56011 18.6001 9.30935 18.4963C9.05859 18.3924 8.78683 18.3491 8.51621 18.3701C8.24559 18.3911 7.98375 18.4757 7.752 18.617C6.209 19.557 4.442 17.791 5.382 16.247C5.5231 16.0153 5.60755 15.7537 5.62848 15.4832C5.64942 15.2128 5.60624 14.9412 5.50247 14.6906C5.3987 14.44 5.23726 14.2174 5.03127 14.0409C4.82529 13.8645 4.58056 13.7391 4.317 13.675C2.561 13.249 2.561 10.751 4.317 10.325C4.5808 10.2611 4.82578 10.1358 5.032 9.95929C5.23822 9.7828 5.39985 9.56011 5.50375 9.30935C5.60764 9.05859 5.65085 8.78683 5.62987 8.51621C5.60889 8.24559 5.5243 7.98375 5.383 7.752C4.443 6.209 6.209 4.442 7.753 5.382C8.753 5.99 10.049 5.452 10.325 4.317Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'feedback' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 12H8.01M12 12H12.01M16 12H16.01M21 12C21 16.4183 16.9706 20 12 20C10.4607 20 9.01172 19.6565 7.74467 19.0511L3 20L4.39499 16.28C3.51156 15.0423 3 13.5743 3 12C3 7.58172 7.02944 4 12 4C16.9706 4 21 7.58172 21 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'settings' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19.4 15C19.2669 15.3016 19.2272 15.6362 19.286 15.9606C19.3448 16.285 19.4995 16.5843 19.73 16.82L19.79 16.88C19.976 17.0657 20.1235 17.2863 20.2241 17.5291C20.3248 17.7719 20.3766 18.0322 20.3766 18.295C20.3766 18.5578 20.3248 18.8181 20.2241 19.0609C20.1235 19.3037 19.976 19.5243 19.79 19.71C19.6043 19.896 19.3837 20.0435 19.1409 20.1441C18.8981 20.2448 18.6378 20.2966 18.375 20.2966C18.1122 20.2966 17.8519 20.2448 17.6091 20.1441C17.3663 20.0435 17.1457 19.896 16.96 19.71L16.9 19.65C16.6643 19.4195 16.365 19.2648 16.0406 19.206C15.7162 19.1472 15.3816 19.1869 15.08 19.32C14.7842 19.4468 14.532 19.6572 14.3543 19.9255C14.1766 20.1938 14.0813 20.5082 14.08 20.83V21C14.08 21.5304 13.8693 22.0391 13.4942 22.4142C13.1191 22.7893 12.6104 23 12.08 23C11.5496 23 11.0409 22.7893 10.6658 22.4142C10.2907 22.0391 10.08 21.5304 10.08 21V20.91C10.0723 20.579 9.96512 20.258 9.77251 19.988C9.5799 19.718 9.31074 19.5117 9 19.4C8.69838 19.2669 8.36381 19.2272 8.03941 19.286C7.71502 19.3448 7.41568 19.4995 7.18 19.73L7.12 19.79C6.93425 19.976 6.71368 20.1235 6.47088 20.2241C6.22808 20.3248 5.96783 20.3766 5.705 20.3766C5.44217 20.3766 5.18192 20.3248 4.93912 20.2241C4.69632 20.1235 4.47575 19.976 4.29 19.79C4.10405 19.6043 3.95653 19.3837 3.85588 19.1409C3.75523 18.8981 3.70343 18.6378 3.70343 18.375C3.70343 18.1122 3.75523 17.8519 3.85588 17.6091C3.95653 17.3663 4.10405 17.1457 4.29 16.96L4.35 16.9C4.58054 16.6643 4.73519 16.365 4.794 16.0406C4.85282 15.7162 4.81312 15.3816 4.68 15.08C4.55324 14.7842 4.34276 14.532 4.07447 14.3543C3.80618 14.1766 3.49179 14.0813 3.17 14.08H3C2.46957 14.08 1.96086 13.8693 1.58579 13.4942C1.21071 13.1191 1 12.6104 1 12.08C1 11.5496 1.21071 11.0409 1.58579 10.6658C1.96086 10.2907 2.46957 10.08 3 10.08H3.09C3.42099 10.0723 3.742 9.96512 4.01198 9.77251C4.28196 9.5799 4.48826 9.31074 4.6 9C4.73312 8.69838 4.77282 8.36381 4.714 8.03941C4.65519 7.71502 4.50054 7.41568 4.27 7.18L4.21 7.12C4.02405 6.93425 3.87653 6.71368 3.77588 6.47088C3.67523 6.22808 3.62343 5.96783 3.62343 5.705C3.62343 5.44217 3.67523 5.18192 3.77588 4.93912C3.87653 4.69632 4.02405 4.47575 4.21 4.29C4.39575 4.10405 4.61632 3.95653 4.85912 3.85588C5.10192 3.75523 5.36217 3.70343 5.625 3.70343C5.88783 3.70343 6.14808 3.75523 6.39088 3.85588C6.63368 3.95653 6.85425 4.10405 7.04 4.29L7.1 4.35C7.33568 4.58054 7.63502 4.73519 7.95941 4.794C8.28381 4.85282 8.61838 4.81312 8.92 4.68H9C9.29577 4.55324 9.54802 4.34276 9.72569 4.07447C9.90337 3.80618 9.99872 3.49179 10 3.17V3C10 2.46957 10.2107 1.96086 10.5858 1.58579C10.9609 1.21071 11.4696 1 12 1C12.5304 1 13.0391 1.21071 13.4142 1.58579C13.7893 1.96086 14 2.46957 14 3V3.09C14.0013 3.41179 14.0966 3.72618 14.2743 3.99447C14.452 4.26276 14.7042 4.47324 15 4.6C15.3016 4.73312 15.6362 4.77282 15.9606 4.714C16.285 4.65519 16.5843 4.50054 16.82 4.27L16.88 4.21C17.0657 4.02405 17.2863 3.87653 17.5291 3.77588C17.7719 3.67523 18.0322 3.62343 18.295 3.62343C18.5578 3.62343 18.8181 3.67523 19.0609 3.77588C19.3037 3.87653 19.5243 4.02405 19.71 4.21C19.896 4.39575 20.0435 4.61632 20.1441 4.85912C20.2448 5.10192 20.2966 5.36217 20.2966 5.625C20.2966 5.88783 20.2448 6.14808 20.1441 6.39088C20.0435 6.63368 19.896 6.85425 19.71 7.04L19.65 7.1C19.4195 7.33568 19.2648 7.63502 19.206 7.95941C19.1472 8.28381 19.1869 8.61838 19.32 8.92V9C19.4468 9.29577 19.6572 9.54802 19.9255 9.72569C20.1938 9.90337 20.5082 9.99872 20.83 10H21C21.5304 10 22.0391 10.2107 22.4142 10.5858C22.7893 10.9609 23 11.4696 23 12C23 12.5304 22.7893 13.0391 22.4142 13.4142C22.0391 13.7893 21.5304 14 21 14H20.91C20.5882 14.0013 20.2738 14.0966 20.0055 14.2743C19.7372 14.452 19.5268 14.7042 19.4 15Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'users' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13M16 3.13C16.8604 3.3503 17.623 3.8507 18.1676 4.55231C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89317 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88M13 7C13 9.20914 11.2091 11 9 11C6.79086 11 5 9.20914 5 7C5 4.79086 6.79086 3 9 3C11.2091 3 13 4.79086 13 7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'file-manager' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 7C3 5.89543 3.89543 5 5 5H9.58579C9.851 5 10.1054 5.10536 10.2929 5.29289L11.7071 6.70711C11.8946 6.89464 12.149 7 12.4142 7H19C20.1046 7 21 7.89543 21 9V17C21 18.1046 20.1046 19 19 19H5C3.89543 19 3 18.1046 3 17V7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 11V15M10 13H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'customers' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 11C17.6569 11 19 9.65685 19 8C19 6.34315 17.6569 5 16 5M16 11C14.3431 11 13 9.65685 13 8C13 6.34315 14.3431 5 16 5M16 11V19M16 5V3M8 13C9.65685 13 11 11.6569 11 10C11 8.34315 9.65685 7 8 7C6.34315 7 5 8.34315 5 10C5 11.6569 6.34315 13 8 13ZM8 13C5.23858 13 3 15.2386 3 18V20H13V18C13 15.2386 10.7614 13 8 13Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'analytics' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 13.5L8 8.5L12 12.5L16 7.5L21 12.5M3 20H21M5 20V16M9 20V13M13 20V15M17 20V11M21 20V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'sla' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 6V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 2V4M22 12H20M12 22V20M4 12H2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',

            'tools' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>';
    }
}
