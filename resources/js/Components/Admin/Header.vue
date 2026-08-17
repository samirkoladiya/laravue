<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const toggleSidebar = () => {
    const body = document.body;
    if (body.classList.contains('sidebar-collapse')) {
        body.classList.remove('sidebar-collapse');
        body.classList.add('sidebar-open');
    } else {
        body.classList.remove('sidebar-open');
        body.classList.add('sidebar-collapse');
    }
};
</script>

<template>
    <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="#" role="button" @click.prevent="toggleSidebar">
                <i class="bi bi-list"></i>
              </a>
            </li>
            
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <span class="user-image rounded-circle shadow d-inline-flex align-items-center justify-content-center bg-secondary-subtle" style="width: 25px; height: 25px; overflow: hidden">
                    <img v-if="user?.photo_url" :src="user.photo_url" alt="" class="w-100 h-100" style="object-fit: cover" />
                    <i v-else class="bi bi-person-fill text-secondary" style="font-size: 0.9rem"></i>
                </span>
                <span class="d-none d-md-inline">{{ user?.name }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <span class="rounded-circle shadow d-inline-flex align-items-center justify-content-center bg-body" style="width: 90px; height: 90px; overflow: hidden">
                      <img v-if="user?.photo_url" :src="user.photo_url" alt="" class="w-100 h-100" style="object-fit: cover" />
                      <i v-else class="bi bi-person-fill text-secondary" style="font-size: 2.5rem"></i>
                  </span>
                  <p>
                    {{ user?.name }}
                    <small>{{ user?.email }}</small>
                    <small v-if="user?.joined">Member since {{ user.joined }}</small>
                  </p>
                </li>
                <!--end::User Image-->
                
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <Link href="/admin/profile" class="btn btn-default btn-flat">Profile</Link>
                  <a
                    href="#"
                    class="btn btn-default btn-flat float-end"
                    @click.prevent="router.post('/admin/logout')"
                  >Sign out</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
</template>
