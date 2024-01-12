<?php if(Auth::user()): ?>
<header class="header js-header" data-module="header">
    <div class="row justify-content-around align-items-center">
        <ul class="menu__wrapper">
            <div class="logo-header">
                <a href="{{ route('app_dashboard') }}">
                    <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="116" height="31.44">
                </a>
            </div>
            <div class="actions-list js-toggle-wrapper">
                <button type="button" id="create_btn" class="button has-plus js-toggle-button h-r_b h-c_b">
                    Créer
                    <span style="width: 31px; height: 32px; background: #150C2D; border-radius: 3px; position: absolute; right: 0; margin-right: 6px; padding: 8px;">
                        <img src="/images/plus.png" alt="" style="width:10px; height:10px;">
                    </span>
                    <!-- <p class="shortcut-create">C</p> -->
                </button>
            </div>
            <li class="menu__item">
                <a href="{{ route('app_dashboard') }}" class="menu__link {{ Route::currentRouteName() == 'app_dashboard' ? 'is-active' : ''}}" id="item-dashboard"> Dashboard
                </a>
                <!--<span class="shortcut"><span class="item"></span><span class="item-border"></span>D</span>-->            
            </li>
            <li class="menu__item">
                <a href="{{ route('app_planning') }}" class="menu__link {{ Route::currentRouteName() == 'app_planning' ? 'is-active' : ''}}" id="item-planning"> Planning
                </a>
                <!-- <span class="shortcut"><span class="item"></span><span class="item-border"></span>L</span> -->
            </li>
            <li class="menu__item">
                <a href="{{ route('app_projects') }}" class="menu__link {{ Route::currentRouteName() == 'app_projects' ? 'is-active' : ''}}" id="item-projects"> Projets </a>
                <!-- <span class="shortcut"><span class="item"></span><span class="item-border"></span>P</span> -->
            </li>
            <li class="menu__item">
                <a href="{{ route('app_talents') }}" class="menu__link {{ Route::currentRouteName() == 'app_talents' ? 'is-active' : ''}}" id="item-contacts"> Contacts </a>
                <!-- <span class="shortcut"><span class="item"></span><span class="item-border"></span>R</span> -->
            </li>
            <li class="menu__item" style="     font-weight: bold;
			position: relative;
			background: linear-gradient(to right, #EB725D 0%, #DB57AD 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;">
                <a href="{{ route('app_explorer') }}" class="menu__explorer-link {{ Route::currentRouteName() == 'app_explorer' ? 'is-active' : ''}}" id="item-explorer" style="">
                    Explorer
                </a>
                <!-- <span class="shortcut" style="color: #8678B4; -webkit-text-fill-color: #8678B4; right: -35px;"><span class="item"></span><span class="item-border"></span>E</span> -->
                <span style="position: absolute;
				top: 0;
				color: #C2A6FF;
				right: 16px;
				-webkit-text-fill-color: #C2A6FF;
				font-weight: 600;
				transform: translate(50%, -50%);
				font-size: 10px;padding-bottom: 5px;">BETA</span>
            </li>
            <div class="account__header">
                <div style="display: flex;">
                    <a href="{{ route('app_explorer_messenger') }}">
                        <notifications-panel :user="{{\Auth::user()}}" ></notifications-panel>
                    </a>
                </div>
                <div class="js-toggle-wrapper">
                @if(Auth::user()->avatar === 'user.jpg')
                        <button type="button" class="button js-toggle-button h-r" style="width: 50px;height: 50px;border-radius: 50%;padding-left: 0">
                            <div class="h-r" style="width: 50px;height: 50px;display: flex;justify-content: center;align-items: center;">
                                <div>{{ strtoupper(Auth::user()->firstname[0].Auth::user()->lastname[0]) }}</div>
                            </div>
                        </button>
                        <div class="account-menu__panel js-toggle-content">
                            <a href="{{ url('/explorer/profile') }}" class="account-menu__link">Profile</a>
                            <button type="button" id="workspaces_toggle" class="account-menu__link">Workspaces</button>
                            <div id="workspaces_content">
                                @foreach(Auth::user()->getWorkspacesAttribute() as $workspace)
                                    <button type="button" class="account-menu__link" :class="({{ $workspace->id }} == {{ \Auth::user()->getCurrentWorkspaceAttribute() }}) ? 'active' : ''" v-on:click="Global._switchWorkspace({{ $workspace->id }})">{{ $workspace->name }}</button>
                                @endforeach
                            </div>
                            <!--<button type="button" class="account-menu__link js-dark-mode-button">Dark mode <span class="switch-button"><span class="switch-button__btn"></span></span></button>-->
                            <a href="{{ url('logout') }}" class="logout-link account-menu__link">Deconnexion</a>
                        </div>
                @else
                        <button type="button" class="button js-toggle-button h-r" style="width: 50px;height: 50px;border-radius: 50%;padding-left: 0">
                            <img src="/upload/avatars/{{Auth::user()->avatar }} " class="img-header" alt="" width="50" height="50" style="width: 100%;height: 100%;border-radius: 50%;object-fit: cover;">
                        </button>
                        <div class="account-menu__panel js-toggle-content">
                            <a href="{{ url('/explorer/profile') }}" class="account-menu__link">Profile</a>
                            <button type="button" id="workspaces_toggle" class="account-menu__link">Workspaces</button>
                            <div id="workspaces_content">
                                @foreach(Auth::user()->getWorkspacesAttribute() as $workspace)
                                    <button type="button" class="account-menu__link" :class="({{ $workspace->id }} == {{ \Auth::user()->getCurrentWorkspaceAttribute() }}) ? 'active' : ''" v-on:click="Global._switchWorkspace({{ $workspace->id }})">{{ $workspace->name }}</button>
                                @endforeach
                            </div>
                            <!--<button type="button" class="account-menu__link js-dark-mode-button">Dark mode <span class="switch-button"><span class="switch-button__btn"></span></span></button>-->
                            <a href="{{ url('logout') }}" class="logout-link account-menu__link">Deconnexion</a>
                        </div>
                @endif
                </div>
            </div>
        </ul>
    </div>
</header>
<?php else: ?>
<header id="kolab-header" class="header js-header" data-module="header">
    <div class="row justify-content-center">
        <div class="header-logo">
            <a href="/">
                <img src="{{ url('/images/ui/logo-kolab.png') }}" class="img-fluid is-theme-dark" alt="kolab">
            </a>
        </div>
        <div class="main-container text-center">
            <p class="title">
                Plateforme collaborative pour la post-production
            </p>
        </div>
        <div class="more">
            <a href="/">En savoir plus</a>
        </div>
    </div>
</header>
<?php endif; ?>

