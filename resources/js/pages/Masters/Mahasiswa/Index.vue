<script lang="ts" setup>
import { ArrowDownIcon, ClockIcon, CloudIcon, PencilIcon, PlusIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useMutation } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { getCurrentInstance, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';
import Datatable from '../../../components/Datatable.vue';
import { downloadHelper } from '../../../helpers/download_helper'
const columns = reactive([
    "NIM",
    "Nama",
    "Kelas",
    "Actions"
])
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const requestDelete = async (id) => {
    return await http.delete(`/mahasiswa/${id}`)
}

const router = useRouter()
const route = useRoute()
const update = ref(false)
const { mutate } = useMutation(requestDelete, {
    onSuccess() {
        toast.success("berhasil menghapus mahasiswa", { position: "top-right" })
        router.resolve({ name: route.name! });
        update.value = true
    },
    onError() {
        toast.error("Terjadi kesalahan", { position: "top-right" })
    },
    onSettled() {
        update.value = false
    },
})
const toast = useToast()



const handleDelete = (id) => {
    Swal.fire({
        title: 'Yakin ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: "Batalkan"
    }).then(async (result) => {
        if (result.isConfirmed) {
            await mutate(id)
        }
    })
}

const reset_req = async () => {
    return await http.post('mahasiswa/reset')
}

const { mutate: reset, isLoading: loading_reset } = useMutation(reset_req, {
    onSuccess() {
        toast.success('Berhasil mereset semua data mahasiswa', { position: "top-right" })
        update.value = true
    },
    onError() {
        toast.error('Terjadi kesalahan', { position: "top-right" })
    }, onSettled() {
        update.value = false
    },
})
const handleReset = () => {
    Swal.fire({
        title: 'Yakin ingin mereset semua data ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Reset',
        cancelButtonText: "Batalkan"
    }).then(async (result) => {
        if (result.isConfirmed) {
            await reset()
        }
    })
}

const download_req = async () => {
    return await http.get('mahasiswa/download-format', { responseType: "blob" })
}

const { mutate: download, status: download_status } = useMutation(download_req, {
    onSuccess(data) {
        downloadHelper(data.data, 'mahasiswa.xlsx')
    }
})

const handleDownload = async () => {
    await download()
}

const batch_remove = async (checkeds) => {
    return await http.post('mahasiswa/delete-selected', { checkeds })
}

const selected = ref<string>("")
const { mutate: batch_remove_handle, status: batch_remove_status } = useMutation(batch_remove, {
    onSuccess() {
        toast.success('Berhasil menghapus data mahasiswa', { position: "top-right" })
        update.value = true
    }, onError() {
        toast.error('Terjadi kesalahan', { position: "top-right" })
    }, onSettled() {
        update.value = false
        selected.value = ""
    },
})
const handleBatch = async (checkeds) => {
    if (selected.value.length) {
        switch (selected.value) {
            case "Hapus":
                await batch_remove_handle(checkeds)
                break;
        }
    }
}

</script>
<template>
    <div class="p-3">
        <h3 class="text-lg mb-4 font-semibold">
            Management Mahasiswa
        </h3>
        <div class="flex lg:flex-row items-start gap-3 flex-wrap">
            <router-link class="btn btn-neutral btn-sm" :to="{ name: 'create-mahasiswa' }">
                <PlusIcon class="h-5 w-5" /> Add new
            </router-link>
            <router-link :to="{ name: 'import-mahasiswa' }" class="btn btn-success btn-sm">
                <CloudIcon class="h-5 w-5" /> Import (Excel)
            </router-link>
            <button :disabled="download_status === 'loading'" @click.prevent="handleDownload"
                class="btn btn-success btn-sm">
                <ArrowDownIcon class="h-5 w-5" />
                {{ download_status === 'loading' ? "Downloading...." : "Download Format (Excel)" }}
            </button>
            <button :disabled="loading_reset" @click.prevent="handleReset" class="btn btn-error btn-outline btn-sm">
                <XMarkIcon class="h-5 w-5" /> Reset Data
            </button>
        </div>
        <Datatable url="mahasiswa" :columns="columns" :update="update" checked="id">
            <template #batchActions="data">
                <form class="flex max-w-sm w-full space-x-4" @submit.prevent="handleBatch(data.checkeds)">
                    <select class="select w-full select-bordered" v-model="selected"
                        :disabled="batch_remove_status === 'loading'">
                        <option value="">--Pilih Actions ({{ data.checkeds.length }})--</option>
                        <option value="Hapus">Hapus ({{ data.checkeds.length }})</option>
                    </select>
                    <button class="btn btn-neutral" type="submit"
                        :disabled="!data.checkeds.length || batch_remove_status === 'loading'">Submit</button>
                </form>
            </template>
            <template #content="data">
                <td>
                    {{ data.data.nim }}
                </td>
                <td>
                    {{ data.data.nama }}
                </td>
                <td>
                    {{ data.data.kelas }}
                </td>
                <th class="flex flex-wrap gap-2">
                    <router-link :to="{ name: 'edit-mahasiswa', params: { id: data.data.id } }"
                        class="btn btn-sm btn-neutral btn-outline">
                        <PencilIcon class="w-4 h-4" />
                    </router-link>
                    <button @click.prevent="handleDelete(data.data.id)" class="btn btn-sm btn-neutral btn-outline">
                        <TrashIcon class="w-4 h-4" />
                    </button>
                    <router-link :to="{ name: 'history-mahasiswa', params: { id: data.data.id } }"
                        class="btn btn-sm btn-neutral btn-outline">
                        <ClockIcon class="w-4 h-4" />
                    </router-link>
                </th>
            </template>
        </DataTable>
    </div>
</template>
<script lang="ts">
export default {

}
</script>
<style lang="">
    
</style>