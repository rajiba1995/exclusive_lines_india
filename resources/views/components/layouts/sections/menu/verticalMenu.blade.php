<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  <div class="app-brand demo" style="background: cornsilk;">
    <a href="{{url('/')}}" class="app-brand-link">
      {{-- <span class="app-brand-logo demo me-1">
        @include('_partials.macros',["height"=>20])
      </span> --}}
      <img src="{{asset('assets/img/logo.svg')}}" alt="" style="width: 185px; height: auto;">
      {{-- <span class="app-brand-text demo menu-text fw-semibold ms-2">Exclusive Lines</span> --}}
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="menu-toggle-icon d-xl-block align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1 ps">

    <li class="menu-item {{ (request()->is('admin/dashboard*')) ? 'open' : '' }}">
      <a href="{{route('admin.dashboard')}}" class="menu-link">
        <i class="menu-icon tf-icons ri-home-smile-line"></i>
        <div>Dashboards</div>
      </a>
    </li>
    <li class="menu-item {{ (request()->is('admin/master*')) ? 'open' : '' }}">
      <a href="#" class="menu-link menu-toggle waves-effect" target="_blank">
        <i class="menu-icon tf-icons ri-stock-line"></i>
        <div>Master Management</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ (request()->is('admin/master/banner*')) ? 'open' : '' }}">
          <a href="{{route('admin.banner.index')}}" class="menu-link">
            <div>Banner</div>
          </a>
        </li>
        <li class="menu-item {{ (request()->is('admin/master/banner*')) ? 'open' : '' }}">
          <a href="{{route('admin.banner.index')}}" class="menu-link">
            <div>Blogs</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item {{ (request()->is('admin/master*')) ? 'open' : '' }}">
      <a href="#" class="menu-link menu-toggle waves-effect" target="_blank">
        <i class="menu-icon tf-icons ri-stock-line"></i>
        <div>Website Settings</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ (request()->is('admin/store_location/index')) ? 'open' : '' }}">
          <a href="{{route('admin.store_location.index')}}" class="menu-link">
            <div>Store Location</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item {{ (request()->is('admin/common_pages*')) ? 'open' : '' }}">
      <a href="#" class="menu-link menu-toggle waves-effect">
        <i class="menu-icon tf-icons ri-stock-line"></i>
        <div>Common Pages</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ (request()->is('admin/common_pages*')) ? 'open' : '' }}">
          <a href="{{ route('admin.common_pages.index') }}" class="menu-link">
            <div>Manage Pages</div>
          </a>
        </li>
      </ul>
    </li>



    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
      <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
    </div>
    <div class="ps__rail-y" style="top: 0px; right: 4px;">
      <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
    </div>
  </ul>
</aside>