<script lang="ts" setup>
import { useMutation } from '@tanstack/vue-query';
import { getCurrentInstance, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';
import ValidationFeedback from '../../components/ValidationFeedback.vue';
const router = useRouter()
const handleBack = () => {
    router.back()
}

const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }
const data = reactive({
    password: null,
    confirm_password: null,
    new_password: null,
})

const changePassword = async () => {
    return await http.post('auth/change-password', { ...data })
}

const toast = useToast()

const validations = ref<any>({})

const { mutate, status } = useMutation(changePassword, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil change password", { position: "top" })
        window.location.reload()
    },
    onError(error: any) {
        switch (error.response.data.context) {
            case "VALIDATIONS":
                validations.value = error.response.data.validations
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

const handleSubmit = async () => {
    await mutate()
}
</script>
<template>
    <div>
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="form-control">
                <input type="password" class="input input-bordered" placeholder="Password" v-model="data.password">
                <ValidationFeedback :validations="validations.password" />
            </div>
            <div class="form-control">
                <input type="password" class="input input-bordered" placeholder="Password Baru" v-model="data.new_password">
                <ValidationFeedback :validations="validations.new_password" />
            </div>
            <div class="form-control">
                <input type="password" class="input input-bordered" placeholder="Konfirmasi Password Baru" v-model="data.confirm_password">
                <ValidationFeedback :validations="validations.confirm_password" />
            </div>
            <div class="flex space-x-2">
                <button class="btn btn-neutral mt-3" type="submit" :disabled="status === 'loading'">
                    {{ status === 'loading' ? "Update..." : "Update" }}
                </button>
                <button class="btn btn-error mt-3" @click.prevent="handleBack">
                    Batal
                </button>
            </div>
        </form>
    </div>
</template>
<script lang="ts">
export default {

}
</script>
<style lang="">
    
</style>