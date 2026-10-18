<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="نظام إدارة العيادات الطبية المتطور - Clinic 1">
    <title>Clinic 1 | المنظومة الطبية الذكية</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        /* Anti-AI Slop Rules from DESIGN_RULES.md: No drop shadows on static containers, clean 1px borders */
        .ui-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .ui-input {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 150ms ease-out;
            width: 100%;
            background-color: #ffffff;
        }
        .ui-input:focus {
            border-color: #0f766e;
        }
        .ui-btn-primary {
            background-color: #0f766e;
            color: #ffffff;
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 150ms ease-out;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .ui-btn-primary:hover {
            background-color: #115e59;
        }
        .ui-btn-secondary {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 150ms ease-out;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .ui-btn-secondary:hover {
            background-color: #f1f5f9;
        }
        .ui-btn-danger {
            background-color: #b91c1c;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 150ms ease-out;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .ui-btn-danger:hover {
            background-color: #991b1b;
        }
        /* Modal Overlay shadow allowed by DESIGN_RULES.md */
        .ui-modal-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand -->
            <div class="flex items-center gap-6">
                <a href="#home-section" onclick="switchView('landing')" class="flex items-center gap-2 text-teal-800 font-bold text-lg">
                    <!-- Clean SVG Clinic Cross Icon -->
                    <svg class="w-6 h-6 text-teal-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Clinic 1</span>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-4 text-sm font-medium text-slate-600">
                    <button onclick="switchView('landing')" class="hover:text-teal-700 py-1 transition-colors">الرئيسية</button>
                    <a href="#about-section" onclick="switchView('landing')" class="hover:text-teal-700 py-1 transition-colors">عن العيادة</a>
                    <a href="#services-section" onclick="switchView('landing')" class="hover:text-teal-700 py-1 transition-colors">الخدمات الطبية</a>

                    <!-- INVENTORY TAB: Disabled & Dark Gray as strictly specified -->
                    <div class="relative group" title="قسم المخزن والصيدلية معطل مؤقتاً لحين تفعيل القسم مستقبلاً">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-400 border border-dashed border-slate-300 cursor-not-allowed select-none opacity-60">
                            <!-- Box/Inventory SVG Icon -->
                            <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>المخزن / الصيدلية (معطل مؤقتاً)</span>
                        </span>
                    </div>
                </nav>
            </div>

            <!-- Auth Status / Controls -->
            <div class="flex items-center gap-3">
                <div id="auth-guest-section" class="flex items-center gap-2">
                    <button onclick="showLoginModal()" class="ui-btn-primary text-sm py-2 px-4">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-5-4l5-5-5-5m5 5H3" />
                        </svg>
                        <span>تسجيل الدخول</span>
                    </button>
                </div>

                <div id="auth-user-section" class="hidden flex items-center gap-3">
                    <div class="text-left text-xs sm:text-sm">
                        <span id="current-user-name" class="font-bold text-slate-800 block"></span>
                        <span id="current-user-role-badge" class="inline-block px-2 py-0.5 text-xs font-medium rounded bg-teal-50 text-teal-800 border border-teal-200"></span>
                    </div>
                    <button onclick="switchView('app-dashboard')" class="ui-btn-secondary text-xs sm:text-sm py-1.5 px-3">
                        <span>لوحة التحكم</span>
                    </button>
                    <button onclick="logoutUser()" class="text-xs text-rose-600 hover:text-rose-800 py-1 px-2 border border-rose-200 rounded hover:bg-rose-50 transition-colors">
                        خروج
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main View Container -->
    <main class="flex-1">

        <!-- ======================================================== -->
        <!-- VIEW 1: COMBINED CLINIC HOME + ABOUT US PAGE            -->
        <!-- ======================================================== -->
        <div id="view-landing" class="block">

            <!-- Hero Section -->
            <section id="home-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-teal-50 text-teal-800 border border-teal-200 mb-6">
                    <span>المنظومة الطبية الرقمية المتكاملة</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-600"></span>
                    <span>معايير جودة الرعاية الصحية 2026</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight max-w-4xl mx-auto mb-6">
                    رعاية طبية متخصصة وإدارة عيادات ذكية ترتكز على صحة المريض
                </h1>

                <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto mb-8 leading-relaxed">
                    توفر عيادة Clinic 1 بيئة استشفائية متطورة تشمل حجز المواعيد الفورية والحالات المستعجلة، إدارة السجلات الطبية بدقة، ونظام فواتير شفاف ومحكم.
                </p>

                <div class="flex flex-wrap justify-center items-center gap-3">
                    <button onclick="showLoginModal()" class="ui-btn-primary py-3 px-6 text-base">
                        <span>الدخول إلى النظام الطبي</span>
                        <svg class="w-4 h-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                    <a href="#about-section" class="ui-btn-secondary py-3 px-6 text-base">
                        <span>تعرف على العيادة وخدماتنا</span>
                    </a>
                </div>

                <!-- Metrics Strip (Clean, No AI Blobs) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-16 max-w-4xl mx-auto pt-10 border-t border-slate-200 text-right">
                    <div class="p-4 bg-white border border-slate-200 rounded-lg">
                        <span class="text-2xl font-bold text-slate-900 block mb-1">15+</span>
                        <span class="text-xs text-slate-500 font-medium">عيادة تخصصية متكاملة</span>
                    </div>
                    <div class="p-4 bg-white border border-slate-200 rounded-lg">
                        <span class="text-2xl font-bold text-slate-900 block mb-1">24/7</span>
                        <span class="text-xs text-slate-500 font-medium">استقبال وحالات مستعجلة</span>
                    </div>
                    <div class="p-4 bg-white border border-slate-200 rounded-lg">
                        <span class="text-2xl font-bold text-slate-900 block mb-1">12,400+</span>
                        <span class="text-xs text-slate-500 font-medium">ملف مريض إلكتروني مؤمّن</span>
                    </div>
                    <div class="p-4 bg-white border border-slate-200 rounded-lg">
                        <span class="text-2xl font-bold text-slate-900 block mb-1">99.4%</span>
                        <span class="text-xs text-slate-500 font-medium">معدل الدقة ورضا المراجعين</span>
                    </div>
                </div>
            </section>

            <!-- MERGED ABOUT US SECTION: Unified with Home page -->
            <section id="about-section" class="bg-white border-y border-slate-200 py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl mb-12">
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-2">نبذة عن العيادة</span>
                        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight mb-4">
                            عن عيادة Clinic 1 ورؤيتنا العلاجية
                        </h2>
                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                            تأسست عيادة Clinic 1 لتقديم خدمات التشخيص والعلاج الطبي بأعلى معايير الدقة والشفافية. تجمع العيادة كادراً طبياً متميزاً مع طاقم استقبال مدرب على إدارة المواعيد وتيسير تجربة المريض منذ دخوله وحتى تمام شفائه.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Quality Point 1 -->
                        <div class="p-6 bg-slate-50 border border-slate-200 rounded-lg">
                            <h3 class="text-base font-bold text-slate-900 mb-2">منظومة الحجز والمسار السريري</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                نعتمد دورة حجز واضحة (قيد الانتظار ⬅️ مؤكد ⬅️ مكتمل ⬅️ ملغي) تضمن التزام المواعيد، مع مسار مخصص للحالات المستعجلة (Urgent Bookings) لتقديم التدخل الطبي الفوري دون تأخير.
                            </p>
                        </div>
                        <!-- Quality Point 2 -->
                        <div class="p-6 bg-slate-50 border border-slate-200 rounded-lg">
                            <h3 class="text-base font-bold text-slate-900 mb-2">حوكمة مالية وفواتير دقيقة</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                نظام فواتير يتيح للاستقبال تطبيق الخصومات المعتمدة قبل السداد، وقفل الفاتورة برمجياً بصيغة القراءة فقط (Read-Only) فور الدفع لحماية الحسابات، مع إتاحة ميزة الاسترجاع المنظم (Refund).
                            </p>
                        </div>
                        <!-- Quality Point 3 -->
                        <div class="p-6 bg-slate-50 border border-slate-200 rounded-lg">
                            <h3 class="text-base font-bold text-slate-900 mb-2">تدرج الصلاحيات وحماية البيانات</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                فصل صارم للمسؤوليات؛ حيث ينفرد المدير بإنشاء حسابات الأطباء والاستقبال، بينما يختص موظف الاستقبال بتسجيل المرضى، مما يحفظ سرية وسلامة السجلات الطبية.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Medical Services Section -->
            <section id="services-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="max-w-3xl mb-10">
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-2">الخدمات السريرية</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">العيادات والخدمات الطبية المتاحة</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="p-5 ui-card">
                        <h4 class="font-bold text-slate-900 mb-2">عيادة الباطنة العامة والتشخيص</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">متابعة الأمراض المزمنة، السكري، وضغط الدم عبر استشاريين متخصصين وفحوصات دورية شاملة.</p>
                    </div>
                    <div class="p-5 ui-card">
                        <h4 class="font-bold text-slate-900 mb-2">عيادة القلب والأوعية الدموية</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">تخطيط ورسم القلب، الفحص بالموجات الصوتية (Echo)، ومتابعة كفاءة عضلة القلب والشرايين.</p>
                    </div>
                    <div class="p-5 ui-card">
                        <h4 class="font-bold text-slate-900 mb-2">عيادة طب وجراحة الأطفال</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">متابعة نمو وحديثي الولادة، برامج التطعيمات المعتمدة، وعلاج الحالات الطارئة للأطفال.</p>
                    </div>
                    <div class="p-5 ui-card">
                        <h4 class="font-bold text-slate-900 mb-2">عيادة جراحة العظام والمفاصل</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">تشخيص وعلاج آلام المفاصل والعمود الفقري، متابعة الكسور، والتأهيل الحركي.</p>
                    </div>
                    <div class="p-5 ui-card">
                        <h4 class="font-bold text-slate-900 mb-2">طب وجراحة الفم والأسنان</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">علاج الجذور واللثة، الحشوات التجميلية، وجراحة الفم الصغرى بأحدث أجهزة التعقيم.</p>
                    </div>
                    <div class="p-5 ui-card border-teal-200 bg-teal-50/30">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-teal-900">قسم الحالات المستعجلة (Urgent)</h4>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-rose-100 text-rose-800">أولوية فورية</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">مسار فوري مخصص للحالات الحرجة والمستعجلة لتخطي قوائم الانتظار تحت إشراف طاقم طوارئ متخصص.</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- ======================================================== -->
        <!-- VIEW 2: APPLICATION DASHBOARDS (ROLE-BASED WORKSPACES)  -->
        <!-- ======================================================== -->
        <div id="view-dashboard" class="hidden max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Dashboard Sub-Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 mb-6 border-b border-slate-200">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900" id="dash-title">لوحة التحكم</h2>
                    <p class="text-xs sm:text-sm text-slate-500" id="dash-subtitle">مرحباً بك في منظومة Clinic 1</p>
                </div>

                <!-- Sub-Navigation for Dashboard Modules -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0" id="dash-nav-buttons">
                    <!-- Dynamic tabs injected by JS based on user role -->
                </div>
            </div>

            <!-- Global Alert Container -->
            <div id="dash-alert" class="hidden mb-6 p-4 rounded-lg text-sm border"></div>

            <!-- ==================================================== -->
            <!-- ADMIN MODULE: Doctor & Receptionist User Creation    -->
            <!-- ==================================================== -->
            <div id="admin-module" class="hidden space-y-8">
                <!-- Info banner -->
                <div class="p-4 bg-teal-50 border border-teal-200 rounded-lg flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-teal-900 text-sm">صلاحيات المدير (Admin Control)</h3>
                        <p class="text-xs text-teal-700">أنت المسؤول الوحيد المفوّض بإنشاء بروفايلات الأطباء وموظفي الاستقبال.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-teal-200/60 text-teal-900">Admin Only</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Form 1: Create Doctor -->
                    <div class="ui-card p-6">
                        <div class="border-b border-slate-200 pb-3 mb-4">
                            <h3 class="font-bold text-slate-900 text-base">إنشاء بروفايل دكتور جديد</h3>
                            <p class="text-xs text-slate-500">خاص بالمدير فقط - إضافة طبيب إلى كادر العيادة</p>
                        </div>
                        <form id="create-doctor-form" onsubmit="handleCreateDoctor(event)" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم الدكتور</label>
                                <input type="text" id="doc-name" required placeholder="مثال: name 3" class="ui-input">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">البريد الإلكتروني</label>
                                <input type="email" id="doc-email" required placeholder="doctor@clinic.com" class="ui-input">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الهاتف</label>
                                <input type="text" id="doc-phone" required placeholder="01012345678" class="ui-input">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور المبدئية</label>
                                <input type="password" id="doc-password" required placeholder="••••••••" class="ui-input">
                            </div>
                            <button type="submit" class="ui-btn-primary w-full py-2.5">
                                <span>حفظ وإنشاء حساب الطبيب</span>
                            </button>
                        </form>
                    </div>

                    <!-- Form 2: Create Receptionist -->
                    <div class="ui-card p-6">
                        <div class="border-b border-slate-200 pb-3 mb-4">
                            <h3 class="font-bold text-slate-900 text-base">إنشاء بروفايل موظف استقبال</h3>
                            <p class="text-xs text-slate-500">خاص بالمدير فقط - تعيين موظف استقبال جديد</p>
                        </div>
                        <form id="create-receptionist-form" onsubmit="handleCreateReceptionist(event)" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم موظف الاستقبال</label>
                                <input type="text" id="rec-name" required placeholder="مثال: name 4" class="ui-input">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">البريد الإلكتروني</label>
                                <input type="email" id="rec-email" required placeholder="receptionist@clinic.com" class="ui-input">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الهاتف</label>
                                <input type="text" id="rec-phone" required placeholder="01098765432" class="ui-input">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور المبدئية</label>
                                <input type="password" id="rec-password" required placeholder="••••••••" class="ui-input">
                            </div>
                            <button type="submit" class="ui-btn-primary w-full py-2.5">
                                <span>حفظ وإنشاء حساب الاستقبال</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Staff Directory List -->
                <div class="ui-card p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-base">سجل الطاقم الطبي والإداري</h3>
                        <button onclick="loadAdminStaffLists()" class="ui-btn-secondary text-xs py-1 px-3">
                            <span>تحديث القائمة</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">قائمة الأطباء المعتمدين</h4>
                            <div id="admin-doctors-list" class="space-y-2 text-sm text-slate-700">
                                <p class="text-slate-400 text-xs">جاري التحميل...</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">قائمة موظفي الاستقبال</h4>
                            <div id="admin-receptionists-list" class="space-y-2 text-sm text-slate-700">
                                <p class="text-slate-400 text-xs">جاري التحميل...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- RECEPTIONIST MODULE: Patients, Appointments, Invoices-->
            <!-- ==================================================== -->
            <div id="receptionist-module" class="hidden space-y-8">
                <!-- Info banner -->
                <div class="p-4 bg-teal-50 border border-teal-200 rounded-lg flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-teal-900 text-sm">مكتب الاستقبال (Reception Desk)</h3>
                        <p class="text-xs text-teal-700">أنت المسؤول عن تسجيل المرضى الجدد، جدولة الحجوزات (العادية والمستعجلة)، وإدارة الفواتير والخصومات والاسترجاع.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-teal-200/60 text-teal-900">Receptionist Only</span>
                </div>

                <!-- Section A: Register New Patient Form -->
                <div class="ui-card p-6">
                    <div class="border-b border-slate-200 pb-3 mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base">تسجيل مريض جديد</h3>
                            <p class="text-xs text-slate-500">خاص بموظف الاستقبال - إنشاء ملف المريض والسجل الطبي</p>
                        </div>
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-700">صلاحية الاستقبال</span>
                    </div>
                    <form id="create-patient-form" onsubmit="handleCreatePatient(event)" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المريض ثلاثي</label>
                            <input type="text" id="pat-name" required placeholder="مثال: name 5" class="ui-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الهاتف</label>
                            <input type="text" id="pat-phone" required placeholder="01234567890" class="ui-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الرقم القومي</label>
                            <input type="text" id="pat-national-id" placeholder="14 رقم" class="ui-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">تاريخ الميلاد</label>
                            <input type="date" id="pat-dob" class="ui-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">النوع</label>
                            <select id="pat-gender" class="ui-input">
                                <option value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">العنوان</label>
                            <input type="text" id="pat-address" placeholder="المدينة، المحافظة" class="ui-input">
                        </div>
                        <div class="sm:col-span-2 md:col-span-3 pt-2">
                            <button type="submit" class="ui-btn-primary py-2.5 px-6">
                                <span>حفظ وتسجيل المريض</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Section B: Appointments Manager with Stepper and Urgent Flag -->
                <div class="ui-card p-6">
                    <div class="border-b border-slate-200 pb-3 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base">إدارة الحجوزات والمواعيد الطبية</h3>
                            <p class="text-xs text-slate-500">مسار الحالات المعتمد: Pending ⬅️ Confirmed ⬅️ Completed ⬅️ Cancelled</p>
                        </div>
                        <button onclick="toggleAppointmentForm()" class="ui-btn-primary text-xs py-2 px-3">
                            <span>+ حجز موعد جديد</span>
                        </button>
                    </div>

                    <!-- Booking Form (Hidden by default) -->
                    <div id="appointment-create-panel" class="hidden mb-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                        <h4 class="font-bold text-slate-800 text-sm mb-3">حجز جديد لمريض</h4>
                        <form id="create-appointment-form" onsubmit="handleCreateAppointment(event)" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">المريض</label>
                                <select id="apt-patient-id" required class="ui-input"></select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">الطبيب المعالج</label>
                                <select id="apt-doctor-id" required class="ui-input"></select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">موعد الكشف</label>
                                <input type="datetime-local" id="apt-date" required class="ui-input">
                            </div>
                            <!-- URGENT BOOKING FLAG AS SPECIFIED -->
                            <div class="flex items-center gap-2 pt-6">
                                <label class="inline-flex items-center gap-2 cursor-pointer p-2 bg-white border border-rose-200 rounded-md w-full">
                                    <input type="checkbox" id="apt-urgent" class="w-4 h-4 text-rose-600 rounded">
                                    <span class="text-xs font-bold text-rose-700">حجز مستعجل (Urgent)</span>
                                </label>
                            </div>
                            <div class="sm:col-span-2 md:col-span-4">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظات / سبب الزيارة</label>
                                <input type="text" id="apt-notes" placeholder="شكوى المريض أو ملاحظات طبية" class="ui-input">
                            </div>
                            <div class="sm:col-span-2 md:col-span-4 flex items-center gap-2">
                                <button type="submit" class="ui-btn-primary text-xs py-2 px-4">تأكيد الجدولة</button>
                                <button type="button" onclick="toggleAppointmentForm()" class="ui-btn-secondary text-xs py-2 px-4">إلغاء</button>
                            </div>
                        </form>
                    </div>

                    <!-- Appointments Table with Badges & Stepper actions -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs sm:text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-semibold">
                                    <th class="py-3 px-3">رقم</th>
                                    <th class="py-3 px-3">المريض</th>
                                    <th class="py-3 px-3">الطبيب</th>
                                    <th class="py-3 px-3">الموعد</th>
                                    <th class="py-3 px-3">النوع</th>
                                    <th class="py-3 px-3">حالة الحجز (Lifecycle)</th>
                                    <th class="py-3 px-3 text-left">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="appointments-table-body" class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400">جاري تحميل جدول المواعيد...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section C: Invoices & Payments with Read-Only Lock, Discounts & Refund -->
                <div class="ui-card p-6">
                    <div class="border-b border-slate-200 pb-3 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base">إدارة الفواتير والمدفوعات (Invoicing & Billing)</h3>
                            <p class="text-xs text-slate-500">الفاتورة تصبح Read-only بعد الدفع | الخصومات والتعديل قبل الدفع | متاح الاسترجاع (Refund) للاستقبال</p>
                        </div>
                        <button onclick="toggleInvoiceCreateForm()" class="ui-btn-primary text-xs py-2 px-3">
                            <span>+ إنشاء فاتورة جديدة</span>
                        </button>
                    </div>

                    <!-- Invoice Create Form (Hidden by default) -->
                    <div id="invoice-create-panel" class="hidden mb-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                        <h4 class="font-bold text-slate-800 text-sm mb-3">فاتورة علاجية جديدة</h4>
                        <form id="create-invoice-form" onsubmit="handleCreateInvoice(event)" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">المريض</label>
                                    <select id="inv-patient-id" required class="ui-input"></select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">قيمة الخصم المبدئي</label>
                                    <input type="number" step="0.01" id="inv-discount" value="0.00" class="ui-input">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">سبب الخصم</label>
                                    <input type="text" id="inv-discount-reason" placeholder="مثال: خصم افتتاح / تأمين" class="ui-input">
                                </div>
                            </div>

                            <!-- Line items -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">بنود الفاتورة والخدمات الطبية</label>
                                <div id="invoice-items-container" class="space-y-2">
                                    <div class="grid grid-cols-12 gap-2 invoice-item-row">
                                        <input type="text" placeholder="وصف الخدمة (مثال: كشف استشاري)" class="col-span-6 ui-input item-desc" required>
                                        <input type="number" placeholder="الكمية" value="1" min="1" class="col-span-2 ui-input item-qty" required>
                                        <input type="number" step="0.01" placeholder="سعر الوحدة" value="300" class="col-span-4 ui-input item-price" required>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-2">
                                <button type="submit" class="ui-btn-primary text-xs py-2 px-4">إصدار الفاتورة</button>
                                <button type="button" onclick="toggleInvoiceCreateForm()" class="ui-btn-secondary text-xs py-2 px-4">إلغاء</button>
                            </div>
                        </form>
                    </div>

                    <!-- Invoices List -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs sm:text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-semibold">
                                    <th class="py-3 px-3">رقم الفاتورة</th>
                                    <th class="py-3 px-3">المريض</th>
                                    <th class="py-3 px-3">الإجمالي</th>
                                    <th class="py-3 px-3">الخصم</th>
                                    <th class="py-3 px-3">الصافي المطلوب</th>
                                    <th class="py-3 px-3">حالة السداد</th>
                                    <th class="py-3 px-3">حالة القفل (Lock)</th>
                                    <th class="py-3 px-3 text-left">إجراءات الاستقبال</th>
                                </tr>
                            </thead>
                            <tbody id="invoices-table-body" class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-slate-400">جاري تحميل الفواتير...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- DOCTOR MODULE: Doctor's Schedule and Urgent Cases   -->
            <!-- ==================================================== -->
            <div id="doctor-module" class="hidden space-y-6">
                <div class="p-4 bg-teal-50 border border-teal-200 rounded-lg flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-teal-900 text-sm">بوابة الطبيب (Doctor View)</h3>
                        <p class="text-xs text-teal-700">استعراض المواعيد المحجوزة، فحص الحالات المستعجلة، وإتمام الكشف الطبي.</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-teal-200/60 text-teal-900">Doctor View</span>
                </div>

                <div class="ui-card p-6">
                    <h3 class="font-bold text-slate-900 text-base mb-4">قائمة الكشوفات والحالات</h3>
                    <div id="doctor-appointments-list" class="space-y-3">
                        <p class="text-slate-400 text-xs">جاري التحميل...</p>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>Clinic 1 &copy; 2026. جميع الحقوق محفوظة.</p>
            <div class="flex items-center gap-4">
                <span>نظام الحجز والفوترة الطبية v2.4</span>
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>متصل بالخادم</span>
            </div>
        </div>
    </footer>

    <!-- ============================================================ -->
    <!-- MODAL 1: AUTHENTICATION LOGIN MODAL (NO ROLE SELECT DROPDOWN)-->
    <!-- ============================================================ -->
    <div id="login-modal" class="fixed inset-0 z-50 bg-slate-900/40 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg ui-modal-shadow max-w-md w-full p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-base">تسجيل الدخول للنظام</h3>
                <button onclick="hideLoginModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
            </div>

            <!-- Informative Notice: Role dropdown eliminated! -->
            <div class="mb-4 p-3 bg-slate-50 border border-slate-200 rounded-md text-xs text-slate-600 leading-relaxed">
                <strong>ملاحظة أمان:</strong> تم إلغاء حقل تحديد الدور (Role) من الشاشة؛ يحدد النظام الصلاحيات تلقائياً من قاعدة البيانات بمجرد إدخال اسم المستخدم أو البريد وكلمة المرور.
            </div>

            <form id="login-form" onsubmit="handleLoginSubmit(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المستخدم أو البريد الإلكتروني</label>
                    <input type="text" id="login-identifier" required placeholder="admin@clinic.com أو اسم المستخدم" class="ui-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور</label>
                    <input type="password" id="login-password" required placeholder="••••••••" class="ui-input">
                </div>

                <div id="login-error" class="hidden text-xs text-rose-600 p-2 bg-rose-50 border border-rose-200 rounded"></div>

                <button type="submit" id="login-submit-btn" class="ui-btn-primary w-full py-2.5">
                    <span>تسجيل الدخول</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Switcher for Verification & Testing -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <span class="text-[11px] font-semibold text-slate-400 block mb-2">حسابات تجريبية سريعة للتأكد من الصلاحيات:</span>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <button type="button" onclick="fillCredentials('admin@clinic.com', 'password123')" class="p-2 border border-slate-200 rounded hover:bg-slate-50 text-slate-700 text-center">
                        <span class="block font-bold text-teal-800">Admin</span>
                        <span class="text-[10px] text-slate-500">مدير النظام</span>
                    </button>
                    <button type="button" onclick="fillCredentials('receptionist@clinic.com', 'password123')" class="p-2 border border-slate-200 rounded hover:bg-slate-50 text-slate-700 text-center">
                        <span class="block font-bold text-blue-800">Receptionist</span>
                        <span class="text-[10px] text-slate-500">الاستقبال</span>
                    </button>
                    <button type="button" onclick="fillCredentials('doctor@clinic.com', 'password123')" class="p-2 border border-slate-200 rounded hover:bg-slate-50 text-slate-700 text-center">
                        <span class="block font-bold text-emerald-800">Doctor</span>
                        <span class="text-[10px] text-slate-500">طبيب معالج</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL 2: APPLY DISCOUNT MODAL (RECEPTIONIST PRE-PAYMENT)    -->
    <!-- ============================================================ -->
    <div id="discount-modal" class="fixed inset-0 z-50 bg-slate-900/40 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg ui-modal-shadow max-w-sm w-full p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-base">تطبيق خصم قبل السداد</h3>
                <button onclick="hideDiscountModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
            </div>
            <form onsubmit="handleApplyDiscountSubmit(event)" class="space-y-4">
                <input type="hidden" id="discount-invoice-id">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">قيمة الخصم (جنيه)</label>
                    <input type="number" step="0.01" id="discount-amount" required class="ui-input" placeholder="مثال: 50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">سبب الخصم المعتمد</label>
                    <input type="text" id="discount-reason-input" required class="ui-input" placeholder="مثال: خصم نقابة أو خصم حالة خاصة">
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="ui-btn-primary w-full py-2">حفظ الخصم</button>
                    <button type="button" onclick="hideDiscountModal()" class="ui-btn-secondary w-full py-2">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL 3: RECORD PAYMENT MODAL                                -->
    <!-- ============================================================ -->
    <div id="payment-modal" class="fixed inset-0 z-50 bg-slate-900/40 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg ui-modal-shadow max-w-sm w-full p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-base">تسجيل سداد الفاتورة</h3>
                <button onclick="hidePaymentModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
            </div>
            <form onsubmit="handlePaymentSubmit(event)" class="space-y-4">
                <input type="hidden" id="payment-invoice-id">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ المدفوع (جنيه)</label>
                    <input type="number" step="0.01" id="payment-amount" required class="ui-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">طريقة السداد</label>
                    <select id="payment-method" class="ui-input">
                        <option value="cash">نقداً (Cash)</option>
                        <option value="visa">بطاقة ائتمان / فيزا</option>
                        <option value="insurance">تأمين طبي</option>
                    </select>
                </div>
                <p class="text-[11px] text-amber-700 bg-amber-50 p-2 rounded border border-amber-200">
                    تنبيه: فور إتمام السداد بالكامل ستتحول الفاتورة تلقائياً لوضع القراءة فقط (Read-Only) ويقفل التعديل عليها.
                </p>
                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="ui-btn-primary w-full py-2">تأكيد الدفع وقفل الفاتورة</button>
                    <button type="button" onclick="hidePaymentModal()" class="ui-btn-secondary w-full py-2">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL 4: INVOICE REFUND MODAL (RECEPTIONIST EXCLUSIVE)       -->
    <!-- ============================================================ -->
    <div id="refund-modal" class="fixed inset-0 z-50 bg-slate-900/40 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg ui-modal-shadow max-w-sm w-full p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-base">استرجاع الفاتورة (Refund)</h3>
                <button onclick="hideRefundModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
            </div>
            <p class="text-xs text-slate-600 mb-3">
                صلاحية خاصة بموظف الاستقبال لرد قيمة الفاتورة المسددة بعد التحقق.
            </p>
            <form onsubmit="handleRefundSubmit(event)" class="space-y-4">
                <input type="hidden" id="refund-invoice-id">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">سبب الاسترجاع</label>
                    <textarea id="refund-reason" rows="3" required placeholder="مثال: اعتذار الطبيب لظرف طارئ أو خطأ في الحجز" class="ui-input"></textarea>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="ui-btn-danger w-full py-2">تأكيد الاسترجاع</button>
                    <button type="button" onclick="hideRefundModal()" class="ui-btn-secondary w-full py-2">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- FRONT-END INTERACTION LOGIC (Vanilla JS with Sanctum API)   -->
    <!-- ============================================================ -->
    <script>
        // State
        let currentUser = null;
        let authToken = localStorage.getItem('clinic_auth_token') || null;
        let cachedPatients = [];
        let cachedDoctors = [];

        // Status Stepper constants
        const STATUS_MAP = {
            'pending': { label: 'قيد الانتظار (Pending)', class: 'bg-amber-50 text-amber-800 border-amber-200' },
            'confirmed': { label: 'مؤكد (Confirmed)', class: 'bg-blue-50 text-blue-800 border-blue-200' },
            'completed': { label: 'مكتمل (Completed)', class: 'bg-emerald-50 text-emerald-800 border-emerald-200' },
            'cancelled': { label: 'ملغي (Cancelled)', class: 'bg-slate-100 text-slate-600 border-slate-200' },
        };

        const INVOICE_STATUS_MAP = {
            'unpaid': { label: 'غير مسددة', class: 'bg-slate-100 text-slate-700 border-slate-200' },
            'partially_paid': { label: 'مسددة جزئياً', class: 'bg-amber-50 text-amber-800 border-amber-200' },
            'paid': { label: 'مسددة بالكامل', class: 'bg-emerald-50 text-emerald-800 border-emerald-200' },
            'refunded': { label: 'مسترجعة (Refunded)', class: 'bg-rose-50 text-rose-800 border-rose-200' },
        };

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', async () => {
            if (authToken) {
                await fetchCurrentUser();
            } else {
                updateAuthUI();
            }
        });

        // Switch main view between landing page and dashboard
        function switchView(viewName) {
            const landing = document.getElementById('view-landing');
            const dashboard = document.getElementById('view-dashboard');

            if (viewName === 'app-dashboard') {
                landing.classList.add('hidden');
                dashboard.classList.remove('hidden');
                renderRoleDashboard();
            } else {
                dashboard.classList.add('hidden');
                landing.classList.remove('hidden');
            }
        }

        // Show/Hide Modals
        function showLoginModal() {
            document.getElementById('login-modal').classList.remove('hidden');
            document.getElementById('login-modal').classList.add('flex');
            document.getElementById('login-error').classList.add('hidden');
        }
        function hideLoginModal() {
            document.getElementById('login-modal').classList.add('hidden');
            document.getElementById('login-modal').classList.remove('flex');
        }

        function fillCredentials(identifier, password) {
            document.getElementById('login-identifier').value = identifier;
            document.getElementById('login-password').value = password;
        }

        // Authentication & Role-based Routing
        async function handleLoginSubmit(event) {
            event.preventDefault();
            const identifier = document.getElementById('login-identifier').value.trim();
            const password = document.getElementById('login-password').value;
            const errBox = document.getElementById('login-error');
            const submitBtn = document.getElementById('login-submit-btn');

            errBox.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'جاري التحقق...';

            try {
                // Call login API with credentials only (NO role provided)
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        username: identifier,
                        password: password
                    })
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || 'فشل تسجيل الدخول. يرجى التحقق من البيانات.');
                }

                // Store token & user
                authToken = data.token;
                currentUser = data.user;
                localStorage.setItem('clinic_auth_token', authToken);

                hideLoginModal();
                updateAuthUI();

                // Role-based Routing as requested
                switchView('app-dashboard');
                showNotification(`تم تسجيل الدخول بنجاح بصلاحية: ${currentUser.role}`, 'success');

            } catch (err) {
                errBox.textContent = err.message;
                errBox.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'تسجيل الدخول';
            }
        }

        async function fetchCurrentUser() {
            try {
                const res = await fetch('/api/auth/me', {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                    }
                });
                if (res.ok) {
                    const data = await res.json();
                    currentUser = data.user;
                    updateAuthUI();
                } else {
                    logoutUser(false);
                }
            } catch (e) {
                logoutUser(false);
            }
        }

        function logoutUser(notify = true) {
            if (authToken) {
                fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json',
                    }
                }).catch(() => {});
            }
            authToken = null;
            currentUser = null;
            localStorage.removeItem('clinic_auth_token');
            updateAuthUI();
            switchView('landing');
            if (notify) showNotification('تم تسجيل الخروج بنجاح.', 'info');
        }

        function updateAuthUI() {
            const guestSec = document.getElementById('auth-guest-section');
            const userSec = document.getElementById('auth-user-section');

            if (currentUser && authToken) {
                guestSec.classList.add('hidden');
                userSec.classList.remove('hidden');
                document.getElementById('current-user-name').textContent = currentUser.name;
                document.getElementById('current-user-role-badge').textContent = `الدور: ${currentUser.role}`;
            } else {
                guestSec.classList.remove('hidden');
                userSec.classList.add('hidden');
            }
        }

        // Render role-specific workspace
        function renderRoleDashboard() {
            if (!currentUser) {
                showLoginModal();
                return;
            }

            const adminMod = document.getElementById('admin-module');
            const recepMod = document.getElementById('receptionist-module');
            const docMod = document.getElementById('doctor-module');
            const titleEl = document.getElementById('dash-title');
            const subtitleEl = document.getElementById('dash-subtitle');

            adminMod.classList.add('hidden');
            recepMod.classList.add('hidden');
            docMod.classList.add('hidden');

            const role = currentUser.role;

            if (role === 'admin' || role === 'owner') {
                titleEl.textContent = 'لوحة إدارة النظام الطبي (Admin Panel)';
                subtitleEl.textContent = 'إدارة الحسابات الطبية، إنشاء بروفايلات الأطباء وموظفي الاستقبال، والإشراف العام.';
                adminMod.classList.remove('hidden');
                loadAdminStaffLists();
            } else if (role === 'receptionist') {
                titleEl.textContent = 'مكتب الاستقبال وشؤون المرضى (Receptionist Desk)';
                subtitleEl.textContent = 'تسجيل المرضى الجدد، إدارة المواعيد العادية والمستعجلة، ومتابعة الفواتير والخصومات والاسترجاع.';
                recepMod.classList.remove('hidden');
                loadReceptionistData();
            } else if (role === 'doctor') {
                titleEl.textContent = 'بوابة الطبيب المعالج (Doctor Portal)';
                subtitleEl.textContent = 'استعراض مواعيد الكشف وجدول العيادة مع تمييز الحالات المستعجلة فوراً.';
                docMod.classList.remove('hidden');
                loadDoctorAppointments();
            } else {
                titleEl.textContent = 'بوابة المريض (Patient Portal)';
                subtitleEl.textContent = 'استعراض بيانات الملف الطبي والمواعيد.';
            }
        }

        // Admin: Load Staff Lists
        async function loadAdminStaffLists() {
            try {
                const [docRes, recRes] = await Promise.all([
                    fetch('/api/admin/doctors', { headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' } }),
                    fetch('/api/admin/receptionists', { headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' } }),
                ]);

                if (docRes.ok) {
                    const docData = await docRes.json();
                    cachedDoctors = docData.data;
                    const docContainer = document.getElementById('admin-doctors-list');
                    if (docData.data.length === 0) {
                        docContainer.innerHTML = '<p class="text-xs text-slate-400">لا يوجد أطباء مسجلين حتى الآن.</p>';
                    } else {
                        docContainer.innerHTML = docData.data.map(d => `
                            <div class="p-3 bg-white border border-slate-200 rounded flex items-center justify-between">
                                <div>
                                    <span class="font-bold block">${d.name}</span>
                                    <span class="text-xs text-slate-500">${d.email} | ${d.phone || 'بدون هاتف'}</span>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">طبيب نشط</span>
                            </div>
                        `).join('');
                    }
                }

                if (recRes.ok) {
                    const recData = await recRes.json();
                    const recContainer = document.getElementById('admin-receptionists-list');
                    if (recData.data.length === 0) {
                        recContainer.innerHTML = '<p class="text-xs text-slate-400">لا يوجد موظفو استقبال مسجلين.</p>';
                    } else {
                        recContainer.innerHTML = recData.data.map(r => `
                            <div class="p-3 bg-white border border-slate-200 rounded flex items-center justify-between">
                                <div>
                                    <span class="font-bold block">${r.name}</span>
                                    <span class="text-xs text-slate-500">${r.email} | ${r.phone || 'بدون هاتف'}</span>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">استقبال نشط</span>
                            </div>
                        `).join('');
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Admin: Create Doctor
        async function handleCreateDoctor(e) {
            e.preventDefault();
            const name = document.getElementById('doc-name').value;
            const email = document.getElementById('doc-email').value;
            const phone = document.getElementById('doc-phone').value;
            const password = document.getElementById('doc-password').value;

            try {
                const res = await fetch('/api/admin/doctors', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name, email, phone, password })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'فشل إنشاء حساب الطبيب');

                showNotification('تم إنشاء بروفايل الطبيب بنجاح.', 'success');
                document.getElementById('create-doctor-form').reset();
                loadAdminStaffLists();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        // Admin: Create Receptionist
        async function handleCreateReceptionist(e) {
            e.preventDefault();
            const name = document.getElementById('rec-name').value;
            const email = document.getElementById('rec-email').value;
            const phone = document.getElementById('rec-phone').value;
            const password = document.getElementById('rec-password').value;

            try {
                const res = await fetch('/api/admin/receptionists', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name, email, phone, password })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'فشل إنشاء حساب موظف الاستقبال');

                showNotification('تم إنشاء بروفايل موظف الاستقبال بنجاح.', 'success');
                document.getElementById('create-receptionist-form').reset();
                loadAdminStaffLists();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        // Receptionist: Load data (Patients, Doctors, Appointments, Invoices)
        async function loadReceptionistData() {
            await Promise.all([
                loadPatients(),
                loadDoctorsForDropdown(),
                loadAppointments(),
                loadInvoices()
            ]);
        }

        async function loadPatients() {
            try {
                const res = await fetch('/api/patients', {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    cachedPatients = data.data;
                    updatePatientSelects();
                }
            } catch (e) { console.error(e); }
        }

        async function loadDoctorsForDropdown() {
            try {
                // Anyone authenticated can load doctors list
                const res = await fetch('/api/admin/doctors', {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    cachedDoctors = data.data;
                    updateDoctorSelects();
                }
            } catch (e) { console.error(e); }
        }

        function updatePatientSelects() {
            const aptSelect = document.getElementById('apt-patient-id');
            const invSelect = document.getElementById('inv-patient-id');
            const opts = cachedPatients.map(p => `<option value="${p.id}">${p.name} (${p.phone || 'بدون هاتف'})</option>`).join('');
            if (aptSelect) aptSelect.innerHTML = opts || '<option value="">لا يوجد مرضى - يرجى تسجيل مريض أولاً</option>';
            if (invSelect) invSelect.innerHTML = opts || '<option value="">لا يوجد مرضى</option>';
        }

        function updateDoctorSelects() {
            const aptDoc = document.getElementById('apt-doctor-id');
            const opts = cachedDoctors.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
            if (aptDoc) aptDoc.innerHTML = opts || '<option value="">لا يوجد أطباء مسجلين</option>';
        }

        // Receptionist: Create Patient
        async function handleCreatePatient(e) {
            e.preventDefault();
            const name = document.getElementById('pat-name').value;
            const phone = document.getElementById('pat-phone').value;
            const national_id = document.getElementById('pat-national-id').value;
            const date_of_birth = document.getElementById('pat-dob').value;
            const gender = document.getElementById('pat-gender').value;
            const address = document.getElementById('pat-address').value;

            try {
                const res = await fetch('/api/patients', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name, phone, national_id, date_of_birth, gender, address })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'فشل تسجيل المريض');

                showNotification('تم تسجيل المريض بنجاح.', 'success');
                document.getElementById('create-patient-form').reset();
                await loadPatients();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        // Appointments Management
        function toggleAppointmentForm() {
            const panel = document.getElementById('appointment-create-panel');
            panel.classList.toggle('hidden');
        }

        async function handleCreateAppointment(e) {
            e.preventDefault();
            const patient_id = document.getElementById('apt-patient-id').value;
            const doctor_id = document.getElementById('apt-doctor-id').value;
            const appointment_date = document.getElementById('apt-date').value;
            const is_urgent = document.getElementById('apt-urgent').checked;
            const notes = document.getElementById('apt-notes').value;

            if (!patient_id || !doctor_id) {
                showNotification('يرجى اختيار المريض والطبيب أولاً.', 'error');
                return;
            }

            try {
                const res = await fetch('/api/appointments', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ patient_id, doctor_id, appointment_date, is_urgent, notes })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'فشل جدولة الموعد');

                showNotification('تم حجز الموعد بنجاح بنمط: ' + (is_urgent ? 'مستعجل (Urgent)' : 'عادي'), 'success');
                document.getElementById('create-appointment-form').reset();
                toggleAppointmentForm();
                loadAppointments();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        async function loadAppointments() {
            try {
                const res = await fetch('/api/appointments', {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    renderAppointmentsTable(data.data);
                }
            } catch (e) { console.error(e); }
        }

        function renderAppointmentsTable(appointments) {
            const tbody = document.getElementById('appointments-table-body');
            if (!tbody) return;

            if (appointments.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="py-6 text-center text-slate-400">لا توجد مواعيد مسجلة حتى الآن.</td></tr>';
                return;
            }

            tbody.innerHTML = appointments.map(apt => {
                const st = STATUS_MAP[apt.status] || { label: apt.status, class: 'bg-slate-100 text-slate-700' };
                const urgentBadge = apt.is_urgent
                    ? `<span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded bg-rose-100 text-rose-800 border border-rose-200">
                         <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                         مستعجل (Urgent)
                       </span>`
                    : `<span class="text-xs text-slate-500 font-medium">عادي</span>`;

                // Lifecycle Stepper Transition Action buttons
                let actions = '';
                if (apt.status === 'pending') {
                    actions = `
                        <button onclick="updateAppointmentStatus(${apt.id}, 'confirmed')" class="text-xs px-2 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded font-medium ml-1">تأكيد (Confirm)</button>
                        <button onclick="updateAppointmentStatus(${apt.id}, 'cancelled')" class="text-xs px-2 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 rounded font-medium">إلغاء</button>
                    `;
                } else if (apt.status === 'confirmed') {
                    actions = `
                        <button onclick="updateAppointmentStatus(${apt.id}, 'completed')" class="text-xs px-2 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 rounded font-medium ml-1">إتمام (Complete)</button>
                        <button onclick="updateAppointmentStatus(${apt.id}, 'cancelled')" class="text-xs px-2 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 rounded font-medium">إلغاء</button>
                    `;
                } else {
                    actions = `<span class="text-xs text-slate-400">مغلق نهائياً</span>`;
                }

                return `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-3 font-medium text-slate-900">#${apt.id}</td>
                        <td class="py-3 px-3 font-semibold">${apt.patient ? apt.patient.name : 'مريض غير محدد'}</td>
                        <td class="py-3 px-3">${apt.doctor ? apt.doctor.name : 'طبيب غير محدد'}</td>
                        <td class="py-3 px-3 text-xs text-slate-600">${new Date(apt.appointment_date).toLocaleString('ar-EG')}</td>
                        <td class="py-3 px-3">${urgentBadge}</td>
                        <td class="py-3 px-3">
                            <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded border ${st.class}">${st.label}</span>
                        </td>
                        <td class="py-3 px-3 text-left">${actions}</td>
                    </tr>
                `;
            }).join('');
        }

        async function updateAppointmentStatus(id, newStatus) {
            try {
                const res = await fetch(`/api/appointments/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'تعذر تحديث حالة الحجز');

                showNotification(`تم تحديث حالة الحجز إلى ${newStatus}.`, 'success');
                loadAppointments();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        // Invoicing & Payments Management
        function toggleInvoiceCreateForm() {
            const panel = document.getElementById('invoice-create-panel');
            panel.classList.toggle('hidden');
        }

        async function handleCreateInvoice(e) {
            e.preventDefault();
            const patient_id = document.getElementById('inv-patient-id').value;
            const discount = parseFloat(document.getElementById('inv-discount').value) || 0.00;
            const discount_reason = document.getElementById('inv-discount-reason').value;

            const itemRows = document.querySelectorAll('.invoice-item-row');
            const items = [];
            itemRows.forEach(row => {
                const desc = row.querySelector('.item-desc').value;
                const qty = parseInt(row.querySelector('.item-qty').value) || 1;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                if (desc) {
                    items.push({ description: desc, quantity: qty, unit_price: price });
                }
            });

            if (items.length === 0) {
                showNotification('يرجى إدخال بند واحد على الأقل في الفاتورة.', 'error');
                return;
            }

            try {
                const res = await fetch('/api/invoices', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ patient_id, discount, discount_reason, items })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'فشل إنشاء الفاتورة');

                showNotification('تم إصدار الفاتورة بنجاح.', 'success');
                document.getElementById('create-invoice-form').reset();
                toggleInvoiceCreateForm();
                loadInvoices();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        async function loadInvoices() {
            try {
                const res = await fetch('/api/invoices', {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    renderInvoicesTable(data.data);
                }
            } catch (e) { console.error(e); }
        }

        function renderInvoicesTable(invoices) {
            const tbody = document.getElementById('invoices-table-body');
            if (!tbody) return;

            if (invoices.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="py-6 text-center text-slate-400">لا توجد فواتير مسجلة.</td></tr>';
                return;
            }

            tbody.innerHTML = invoices.map(inv => {
                const st = INVOICE_STATUS_MAP[inv.payment_status] || { label: inv.payment_status, class: 'bg-slate-100 text-slate-700' };

                // Read-only Lock Indicator as strictly requested
                const lockBadge = inv.is_read_only
                    ? `<span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 border border-emerald-300">
                         <svg class="w-3 h-3 text-emerald-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                           <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                           <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                         </svg>
                         Read-Only (مقفلة)
                       </span>`
                    : `<span class="inline-flex items-center gap-1 text-[11px] font-medium px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200">
                         قابلة للتعديل والخصم
                       </span>`;

                // Actions based on payment_status and receptionist capabilities
                let actions = '';
                if (!inv.is_read_only && inv.payment_status !== 'paid' && inv.payment_status !== 'refunded') {
                    // Pre-payment Receptionist Controls: Apply Discount & Pay
                    actions = `
                        <button onclick="openDiscountModal(${inv.id})" class="text-xs px-2 py-1 bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200 rounded font-medium ml-1">تطبيق خصم</button>
                        <button onclick="openPaymentModal(${inv.id}, ${inv.total_amount})" class="text-xs px-2.5 py-1 bg-teal-700 text-white hover:bg-teal-800 rounded font-medium">سداد</button>
                    `;
                } else if (inv.payment_status === 'paid') {
                    // Post-payment Receptionist capability: Refund button
                    actions = `
                        <button onclick="openRefundModal(${inv.id})" class="text-xs px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded font-semibold">عمل استرجاع (Refund)</button>
                    `;
                } else if (inv.payment_status === 'refunded') {
                    actions = `<span class="text-xs text-rose-600 font-medium">تم الاسترجاع</span>`;
                }

                return `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-3 font-medium text-slate-900">INV-${inv.id}</td>
                        <td class="py-3 px-3 font-semibold">${inv.patient ? inv.patient.name : 'مريض غير محدد'}</td>
                        <td class="py-3 px-3">${parseFloat(inv.subtotal).toFixed(2)} ج.م</td>
                        <td class="py-3 px-3 text-amber-700 font-medium">${parseFloat(inv.discount).toFixed(2)} ج.م</td>
                        <td class="py-3 px-3 font-bold text-slate-900">${parseFloat(inv.total_amount).toFixed(2)} ج.م</td>
                        <td class="py-3 px-3">
                            <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded border ${st.class}">${st.label}</span>
                        </td>
                        <td class="py-3 px-3">${lockBadge}</td>
                        <td class="py-3 px-3 text-left">${actions}</td>
                    </tr>
                `;
            }).join('');
        }

        // Discount Modal logic
        function openDiscountModal(invoiceId) {
            document.getElementById('discount-invoice-id').value = invoiceId;
            document.getElementById('discount-amount').value = '';
            document.getElementById('discount-reason-input').value = '';
            document.getElementById('discount-modal').classList.remove('hidden');
            document.getElementById('discount-modal').classList.add('flex');
        }
        function hideDiscountModal() {
            document.getElementById('discount-modal').classList.add('hidden');
            document.getElementById('discount-modal').classList.remove('flex');
        }
        async function handleApplyDiscountSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('discount-invoice-id').value;
            const discount = parseFloat(document.getElementById('discount-amount').value);
            const discount_reason = document.getElementById('discount-reason-input').value;

            try {
                const res = await fetch(`/api/invoices/${id}/discount`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ discount, discount_reason })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'فشل تطبيق الخصم');

                showNotification('تم تطبيق الخصم بنجاح قبل السداد.', 'success');
                hideDiscountModal();
                loadInvoices();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        // Payment Modal logic
        function openPaymentModal(invoiceId, totalAmount) {
            document.getElementById('payment-invoice-id').value = invoiceId;
            document.getElementById('payment-amount').value = totalAmount;
            document.getElementById('payment-modal').classList.remove('hidden');
            document.getElementById('payment-modal').classList.add('flex');
        }
        function hidePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
            document.getElementById('payment-modal').classList.remove('flex');
        }
        async function handlePaymentSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('payment-invoice-id').value;
            const amount_paid = parseFloat(document.getElementById('payment-amount').value);
            const method = document.getElementById('payment-method').value;

            try {
                const res = await fetch(`/api/invoices/${id}/pay`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ amount_paid, method })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'فشل تسجيل الدفع');

                showNotification('تم السداد بنجاح وقُفلت الفاتورة كـ Read-only.', 'success');
                hidePaymentModal();
                loadInvoices();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        // Refund Modal logic (Receptionist exclusive)
        function openRefundModal(invoiceId) {
            document.getElementById('refund-invoice-id').value = invoiceId;
            document.getElementById('refund-reason').value = '';
            document.getElementById('refund-modal').classList.remove('hidden');
            document.getElementById('refund-modal').classList.add('flex');
        }
        function hideRefundModal() {
            document.getElementById('refund-modal').classList.add('hidden');
            document.getElementById('refund-modal').classList.remove('flex');
        }
        async function handleRefundSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('refund-invoice-id').value;
            const reason = document.getElementById('refund-reason').value;

            try {
                const res = await fetch(`/api/invoices/${id}/refund`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reason })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'فشل عمل الاسترجاع');

                showNotification('تم عمل الاسترجاع (Refund) بنجاح.', 'success');
                hideRefundModal();
                loadInvoices();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }

        // Doctor Portal
        async function loadDoctorAppointments() {
            try {
                const res = await fetch('/api/appointments', {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    const container = document.getElementById('doctor-appointments-list');
                    if (data.data.length === 0) {
                        container.innerHTML = '<p class="text-xs text-slate-400">لا توجد كشوفات محجوزة حالياً.</p>';
                        return;
                    }

                    // Sort urgent first
                    const sorted = [...data.data].sort((a, b) => (b.is_urgent ? 1 : 0) - (a.is_urgent ? 1 : 0));

                    container.innerHTML = sorted.map(apt => {
                        const st = STATUS_MAP[apt.status] || { label: apt.status, class: 'bg-slate-100 text-slate-700' };
                        const urgentHighlight = apt.is_urgent ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200 bg-white';
                        const urgentTag = apt.is_urgent ? '<span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold text-xs">مستعجل (Urgent)</span>' : '';

                        let actionBtn = '';
                        if (apt.status === 'confirmed') {
                            actionBtn = `<button onclick="updateAppointmentStatus(${apt.id}, 'completed')" class="ui-btn-primary text-xs py-1.5 px-3">إتمام الكشف</button>`;
                        } else if (apt.status === 'pending') {
                            actionBtn = `<button onclick="updateAppointmentStatus(${apt.id}, 'confirmed')" class="ui-btn-secondary text-xs py-1.5 px-3">تأكيد الموعد</button>`;
                        }

                        return `
                            <div class="p-4 rounded-lg border ${urgentHighlight} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-slate-900">${apt.patient ? apt.patient.name : 'مريض'}</span>
                                        ${urgentTag}
                                        <span class="text-xs px-2 py-0.5 rounded border ${st.class}">${st.label}</span>
                                    </div>
                                    <p class="text-xs text-slate-500">الموعد: ${new Date(apt.appointment_date).toLocaleString('ar-EG')} | هاتف: ${apt.patient ? apt.patient.phone : '-'}</p>
                                    ${apt.notes ? `<p class="text-xs text-slate-600 mt-1 bg-slate-50 p-1.5 rounded">ملاحظة: ${apt.notes}</p>` : ''}
                                </div>
                                <div>${actionBtn}</div>
                            </div>
                        `;
                    }).join('');
                }
            } catch (e) { console.error(e); }
        }

        // Global Alert Helper
        function showNotification(msg, type = 'info') {
            const el = document.getElementById('dash-alert');
            el.textContent = msg;
            el.className = 'mb-6 p-4 rounded-lg text-sm border';

            if (type === 'success') {
                el.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
            } else if (type === 'error') {
                el.classList.add('bg-rose-50', 'text-rose-800', 'border-rose-200');
            } else {
                el.classList.add('bg-blue-50', 'text-blue-800', 'border-blue-200');
            }

            el.classList.remove('hidden');
            setTimeout(() => { el.classList.add('hidden'); }, 6000);
        }
    </script>
</body>
</html>
