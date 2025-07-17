<script setup>

import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import Cookies from 'js-cookie';
import api from '../../helpers/axios.js';

const router = useRouter()

const user = reactive({
    email: '',
    password: ''
})

const validation = ref([])
const successMessage = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const loginAttempt = async () => {
    // clear message
    errorMessage.value = ''
    validation.value = []
    isLoading.value = true

    try {
        const response = await api.post('/api/login', {
            email: user.email,
            password: user.password
        }) 

        // set token & user on cookies
        Cookies.set('token', response.data.data.token)
        Cookies.set('user', JSON.stringify(response.data.data.user))

        successMessage.value = response.data.message

        // verify token
        if (Cookies.get('token')) {
            // delay 1 detik
            setTimeout(() => {
                // redirect to dashboard
                router.push({ name: 'dashboard' })
            })
        }
    } catch (error) {
        const responseData = error.response?.data || {}

        errorMessage.value = responseData.message
        validation.value = {
            errors: responseData.errors
        }
    } finally {
        isLoading.value = false
    }
}
</script>

<template>
    <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Form Login</h3></div>
                                    <div class="card-body">
                                        <!-- Success Message -->
                                        <div v-if="successMessage" class="alert alert-success mt-2">
                                            {{ successMessage }}
                                        </div>

                                        <!-- Error Message -->
                                        <div v-if="errorMessage || validation.errors" class="alert alert-danger mt-2" role="alert">

                                            <!-- General Error -->
                                            <div v-if="errorMessage">{{ errorMessage }}</div>

                                            <!-- Validation Errors -->
                                            <ul v-if="validation.errors" class="mb-0 mt-2">
                                                <li v-for="(messages, field) in validation.errors" :key="field">
                                                    <span v-for="(message, index) in messages" :key="index">
                                                        {{ field }} : {{ message }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>

                                        <form @submit.prevent="loginAttempt">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" v-model="user.email" id="inputEmail" type="email" placeholder="name@example.com" />
                                                <label for="inputEmail">Email address</label>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <input class="form-control" v-model="user.password" id="inputPassword" type="password" placeholder="Password" />
                                                <label for="inputPassword">Password</label>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button type="submit" class="btn btn-primary w-100 shadow-sm" :disable="isLoading">
                                                    <span v-if="isLoading">
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        Loading...
                                                    </span>

                                                    <span v-else>
                                                        Login
                                                    </span>
                                                </button>
                                            </div>


                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="register.html">Need an account? Sign up!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
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
            </div>
        </div>
</template>
