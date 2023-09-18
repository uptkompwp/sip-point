<script lang="ts" setup>
import { useMutation } from '@tanstack/vue-query';
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toast-notification';
import ValidationFeedback from '../../components/ValidationFeedback.vue';
import { checkValidation } from '../../helpers/validation_helper';
import icon from '../../images/icon.png'

const { auth } = getCurrentInstance()?.appContext.app as unknown as { auth: any }
const credentials = reactive<{ username: string | null, password: string | null }>({
    username: null,
    password: null
})

const validations = ref<any>({})
const toast = useToast()

const loginRequest = async () => {
    return new Promise(async (resolve, reject) => {
        try {
            resolve(await auth.login({
                data: credentials
            }))
        } catch (err) {
            reject(err)
        }
    })
}

const { mutate, isLoading } = useMutation(loginRequest, {
    onMutate() {
        validations.value = {}
    },
    onError(error: any) {
        switch (error.response?.data?.context) {
            case "VALIDATIONS":
                validations.value = error.response?.data?.validations
                break;
            case "INVALID_CREDENTIALS":
                toast.error('Invalid username atau password', { position: 'top' })
                break;
            case "INTERNAL_SERVER_ERROR":
                toast.error('Internal server error', { position: 'top' })
                break;
            default:
                toast.error('Internal server error', { position: 'top' })
                break;
        }

    },
})

const handleLogin = async () => {
    await mutate()
}

onMounted(() => {
    toast.clear()
})

</script>
<template>
    <div class="min-h-[496.8px] grid place-items-center">
        <div class="lg:w-[360px] w-[90%]">
            <div class="mb-5 p-1">
                <div class="flex items-center justify-center">
                    <img :src="icon" alt="LOGO UPT" class="h-12 w-12">
                    <h2 class="ml-2 font-semibold text-xl">SIP POINT</h2>
                </div>
            </div>
            <div class="card border bg-white">
                <div class="card-header p-3">
                    <p class="text-center text-gray-500 text-sm">
                        Silahkan login di SIP POINT
                    </p>
                </div>
                <form @submit.prevent="handleLogin">
                    <div class="card-body p-3">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Username</span>
                            </label>
                            <input :disabled="isLoading" type="text" placeholder="Masukan username"
                                :class="`input input-bordered w-full ${checkValidation(validations.username)}`"
                                v-model="credentials.username" />
                            <ValidationFeedback :validations="validations.username" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Password</span>
                            </label>
                            <input :disabled="isLoading" type="password" placeholder="Masukan password"
                                :class="`input input-bordered w-full ${checkValidation(validations.password)}`"
                                v-model="credentials.password" />
                            <ValidationFeedback :validations="validations.password" />
                        </div>
                    </div>
                    <div class="card-footer p-4 grid">
                        <button type="submit" class="btn btn-neutral" :disabled="isLoading">
                            <span class="loading loading-spinner" v-if="isLoading"></span>
                            LOGIN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script lang="ts">

export default {
}
</script>
<style lang="">
    
</style>