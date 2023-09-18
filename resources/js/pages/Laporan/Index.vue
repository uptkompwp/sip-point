<script setup lang="ts">
import { Axios } from 'axios';
import { getCurrentInstance, reactive, ref } from 'vue';
import { downloadHelper } from '../../helpers/download_helper'
import SearchInput from '../../components/SearchInput.vue';
import { useMutation } from '@tanstack/vue-query';
import { useToast } from 'vue-toast-notification';
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }
const data = reactive<{ makul: any | null, kelas: any | null }>({ makul: null, kelas: null })
const toast = useToast()
const genereteReport = async () => {
    const queryParams = new URLSearchParams({ kelas: data.kelas ? data.kelas.value ?? "" : "", makul: data.makul ? data.makul.value ?? "" : "" })
    return await (http as Axios).get(`/report/export?${queryParams}`, { responseType: "blob" });
}


const { status, mutate } = useMutation(genereteReport, {
    onSuccess(report) {
        downloadHelper(report.data, `laporan.xlsx`)
        data.kelas = null
        data.makul = null
    },
    onError(error: any) {
        switch (error.response.status) {
            case 400:
                toast.error('Bad request', { position: 'top' })
                break;
            default:
                toast.error('Internal server error', { position: 'top' })
                break;
        }
    },
})



const getKelas = async (search) => {
    return await http.get(search.length ? `kelas/all?search=${search}` : `kelas/all`)
}
const getMakul = async (search) => {
    return await http.get(search.length ? `mata-kuliah/all?search=${search}` : `mata-kuliah/all`)
}

function setMakul(val) {
    data.makul = val
}
function setKelas(val) {
    data.kelas = val
}

const handleSubmit = async () => {
    await mutate()
}
</script>
<template>
    <div class="p-4 card border lg:max-w-lg w-full rounded-md">
        <h3 class="card-title mb-3">
            Generate Laporan
        </h3>
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="form-control">
                <SearchInput :model="data.kelas" @on-selected="setKelas" value="id" label="kelas"
                    :disabled="status === 'loading'" :request="getKelas" label-input="Cari Kelas"
                    no-opt="Ketik untuk mencari Kelas" />
            </div>
            <div class="form-control">
                <SearchInput :model="data.makul" @on-selected="setMakul" value="id" label="makul"
                    :disabled="status === 'loading'" :request="getMakul" label-input="Cari Mata kuliah"
                    no-opt="Ketik untuk mencari Mata Kuliah" />
            </div>
            <button class="btn btn-neutral mt-3" type="submit" :disabled="status === 'loading'">
                {{ status === 'loading' ? "Generate..." : "Generate Laporan" }}
            </button>
        </form>
    </div>
</template>
<script lang="ts">
export default {

}
</script>
<style lang="">
    
</style>