<script setup>

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
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Start Bootstrap
        </div>
    </nav>
</template>
