<script lang="ts" setup>
import { getCurrentInstance, reactive, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ mahasiswa: any | null, point: string | null }>({ mahasiswa: null, point: null })
const validations = ref<any>({})
const route = useRoute()
const storePoint = async (data) => {
    return await http.post(`/point/${route.params.id}`, { ...data, mahasiswa: data.mahasiswa ? data.mahasiswa.value : null })
}

const toast = useToast()
const { isLoading, mutate } = useMutation(storePoint, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil menambah point", { position: "top" })
        data.mahasiswa = null;
        data.point = null
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
    return await http.get(`/kuis/${route.params.id}/edit`);
}

const router = useRouter()
const { mutate: get_edit, status } = useMutation(getSesi, {
    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('Kuis tidak di temukan', { position: 'top' })
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


const getMhs = async (search) => {
    return await http.get(search.length ? `mahasiswa/all?search=${search}` : `mata-kuliah/all`)
}

function setMhs(val) {
    data.mahasiswa = val
}
</script>
<template>
    <div>
        <div v-if="status === 'success'">
            <Buttonback />
            <div class="p-4 card border lg:max-w-lg w-full rounded-md">
                <h3 class="card-title mb-3">Tambahkan Point Baru</h3>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div class="form-control">
                        <SearchInput :model="data.mahasiswa" @on-selected="setMhs" value="id" label="nim"
                            :disabled="isLoading" :request="getMhs" label-input="Cari NIM mahasiswa"
                            no-opt="Ketik untuk mencari NIM mahasiswa" />
                        <ValidationFeedback :validations="validations.mahasiswa" />
                    </div>
                    <div class="form-control">
                        <input autofocus type="number"
                            :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.kelas)}`"
                            class="input input-bordered w-full max-w-lg" placeholder="Masukan jumlah point yang di dapat"
                            v-model="data.point" :disabled="isLoading">
                        <ValidationFeedback :validations="validations.point" />
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
import ValidationFeedback from '../../../components/ValidationFeedback.vue';
import Loading from '../../../components/Loading.vue'
import SearchInput from '../../../components/SearchInput.vue';
import { checkValidation } from '../../../helpers/validation_helper';
export default {
    components: { Buttonback, Loading }
}
</script>
<style lang="">
    
</style>