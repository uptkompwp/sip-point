<script lang="ts" setup>
import { getCurrentInstance, reactive, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ kuis: string | null, type: string }>({ kuis: null, type: "" })
const validations = ref<any>({})
const route = useRoute()
const storeKuis = async (data) => {
    return await http.post(`/kuis/${route.params.sesi_id}/sesi`, { ...data })
}

const toast = useToast()
const { isLoading, mutate } = useMutation(storeKuis, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil menambah kuis", { position: "top" })
        data.kuis = null
        data.type = ""
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

const getSesi = async () => {
    return await http.get(`/sesi/${route.params.sesi_id}/edit`);
}

const router = useRouter()
const { mutate: get_edit, status } = useMutation(getSesi, {
    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('Sesi tidak di temukan', { position: 'top' })
                router.back()
                break;
            default:
                toast.error('Internal server error', { position: 'top' })
                router.back()
                break;
        }
    },
})


onMounted(async () => {
    toast.clear();
    await get_edit()
})


const handleSubmit = async () => {
    await mutate(data)
}

onMounted(() => {
    toast.clear();
})

</script>
<template>
    <div>
        <div v-if="status === 'success'">
            <Buttonback />
            <div class="p-4 card border lg:max-w-lg w-full rounded-md">
                <h3 class="card-title mb-3">Tambahkan Kuis baru</h3>
                <form @submit.prevent="handleSubmit" class="space-y-4">

                    <input autofocus type="text"
                        :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.kelas)}`"
                        class="input input-bordered w-full max-w-lg" placeholder="Masukan nama kuis" v-model="data.kuis"
                        :disabled="isLoading">
                    <ValidationFeedback :validations="validations.kuis" />
                    <div class="form-control">
                        <label for="tipe_kuis" class="mb-2">Tipe Kuis</label>
                        <select class="select select-bordered" v-model="data.type">
                            <option value="">Pilih Tipe Kuis</option>
                            <option value="PRAKTEK">Praktek</option>
                            <option value="TEORI">Teori</option>
                        </select>
                        <ValidationFeedback :validations="validations.type" />
                    </div>

                    <button class="btn btn-neutral" type="submit" :disabled="isLoading">
                        <span class="loading loading-spinner" v-if="isLoading"></span>
                        Simpan
                    </button>
                </form>
            </div>
        </div>
        <Loading v-if="status === 'loading'" />
    </div>
</template>
<script lang="ts">
import { useMutation } from '@tanstack/vue-query';
import Buttonback from '../../../components/Buttonback.vue';
import { useToast } from 'vue-toast-notification';
import { checkValidation } from '../../../helpers/validation_helper';
import ValidationFeedback from '../../../components/ValidationFeedback.vue';
import Loading from '../../../components/Loading.vue'
export default {
    components: { Buttonback, Loading }
}
</script>
<style lang="">
    
</style>