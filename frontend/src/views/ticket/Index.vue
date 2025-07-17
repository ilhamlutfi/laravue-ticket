<script setup>
import DefaultLayout from '../layout/DefaultLayout.vue';
import { ref, onMounted, nextTick, onUnmounted } from 'vue';
import api from '../../helpers/axios';
import Cookies from 'js-cookie';

const token = Cookies.get('token');

const tickets = ref([]);
const successMessage = ref('');
const errorMessage = ref('');
const isTableReady = ref(false);
let datatableInstance = null;

const initDatatable = () => {
    const table = document.getElementById('datatablesSimple');
    if (table) {
        datatableInstance = new simpleDatatables.DataTable(table);
    }
};

const destroyDatatable = () => {
    if (datatableInstance) {
        datatableInstance.destroy();
        datatableInstance = null;
    }
};

const fetchTickets = async () => {
    isTableReady.value = false;
    destroyDatatable();

    try {
        const response = await api.get('/api/tickets', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        tickets.value = response.data.data;

        await nextTick(); // tunggu DOM render ulang
        isTableReady.value = true;

        await nextTick(); // tunggu <table> muncul di DOM
        initDatatable();
    } catch (error) {
        console.error(error);
    }
};

const deleteTicket = async (code) => {
    const confirmed = confirm('Are you sure you want to delete this ticket?');
    if (!confirmed) return;

    try {
        await api.delete(`/api/tickets/${code}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        successMessage.value = 'Ticket deleted successfully.';
        await fetchTickets();
    } catch (error) {
        const responseData = error.response?.data || {};
        errorMessage.value = responseData.message || 'Something went wrong.';
    }
};

onMounted(() => {
    fetchTickets();
});

onUnmounted(() => {
    destroyDatatable();
});
</script>


<template>
    <DefaultLayout>
        
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4"><i class="fas fa-ticket"></i> Ticket</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Ticket List</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Table Data
                        </div>

                        <div class="card-body">
                            <div v-if="successMessage" class="alert alert-success">
                                {{ successMessage }}
                            </div>

                            <div v-if="errorMessage" class="alert alert-danger">
                                {{ errorMessage }}
                            </div>

                            <table id="datatablesSimple" v-if="isTableReady">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Code</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Attempt By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr v-for="(ticket, index) in tickets" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ ticket.code }}</td>
                                        <td>{{ ticket.priority }}</td>
                                        <td>{{ ticket.status }}</td>
                                        <td>{{ ticket.created_at }}</td>
                                        <td>{{ ticket.user.name }}</td>
                                        <td>
                                            <button @click="deleteTicket(ticket.code)" class="btn btn-sm btn-danger rounded-sm shadow border-0">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div v-else class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
    </DefaultLayout>
</template>
