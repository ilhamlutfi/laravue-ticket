<script setup>
    import { onMounted } from 'vue';
    import { ScriptsSBAdmin } from '../../assets/js/scripts.js';
    import NavBar from '../../components/NavigationBar.vue';
    import SideBar from '../../components/SideBar.vue';

    onMounted(() => {
        ScriptsSBAdmin();
    });

</script>

<template>
    <div>
        <!-- Navbar -->
        <NavBar />

        <div id="layoutSidenav">
            <!-- Sidebar -->
            <div id="layoutSidenav_nav">
                <SideBar />
            </div>

            <!-- Content -->
            <div id="layoutSidenav_content">
                <slot />
            </div>
        </div>
    </div>
</template>
