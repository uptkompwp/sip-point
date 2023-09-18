<script lang="ts" setup>
import { useRoute } from 'vue-router'
import { getCurrentInstance, reactive, ref } from 'vue'
import { useMutation } from '@tanstack/vue-query';
import { useToast } from 'vue-toast-notification';
import ValidationFeedback from '../../components/ValidationFeedback.vue';
const route = useRoute()
const { auth, axios: http } = getCurrentInstance()?.appContext.app as unknown as { auth: any, axios: any }
const data = reactive({
    nama: auth.user() != null ? auth.user().nama : null,
    username: auth.user() != null ? auth.user().username : null
})

const updateProfile = async () => {
    return await http.post('auth/profile', { ...data })
}

const toast = useToast()

const validations = ref<any>({})

const { mutate, status } = useMutation(updateProfile, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil update profile", { position: "top" })
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
    <div
        :class="`p-4 card border ${route.name === 'profile-change-password' ? `lg:max-w-5xl` : `lg:max-w-lg`} w-full rounded-md`">
        <h3 class="card-title mb-3">
            Profile
        </h3>
        <div :class="`grid  ${route.name === 'profile-change-password' ? `lg:grid-cols-2` : ``}  gap-4`">
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div class="form-control">
                    <input type="text" class="input input-bordered" placeholder="Nama" v-model="data.nama">
                    <ValidationFeedback :validations="validations.nama" />

                </div>
                <div class="form-control">
                    <input type="text" class="input input-bordered" placeholder="Username" v-model="data.username">
                    <ValidationFeedback :validations="validations.username" />
                </div>
                <div class="flex space-x-2">
                    <button class="btn btn-neutral mt-3" type="submit" :disabled="status === 'loading'">
                        {{ status === 'loading' ? "Update..." : "Update" }}
                    </button>
                    <router-link :to="{ name: 'profile-change-password' }" class="btn btn-neutral mt-3" type="submit">
                        Ubah Password
                    </router-link>
                </div>
            </form>
            <div v-if="route.name === 'profile-change-password'">
                <router-view></router-view>
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