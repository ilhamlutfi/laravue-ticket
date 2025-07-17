<script setup>
import { useRouter } from 'vue-router';
import { ref } from 'vue';
import Cookies from 'js-cookie';

const router = useRouter();

const successMessage = ref('');

const logoutAttempt = () => {
    const confirmed = confirm('Are you sure you want to logout?');
    if (!confirmed) return;

    Cookies.remove('token')
    Cookies.remove('user')

    successMessage.value = 'Logout attempt successfully'

    router.push({ name: 'login', query: { message: successMessage.value } })
}

</script>

<template>
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Menu</div>

                <router-link :to="{ name: 'dashboard'}" class="nav-link">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </router-link>

                <router-link :to="{ name: 'ticket.index'}" class="nav-link">
                    <div class="sb-nav-link-icon"><i class="fas fa-ticket"></i></div>
                    Ticket
                </router-link>

                <a href="#" class="nav-link" @click="logoutAttempt">
                    <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                    Logout
                </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Start Bootstrap
        </div>
    </nav>
</template>
