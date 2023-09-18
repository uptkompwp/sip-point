<script lang="ts" setup>
import { FolderIcon, PencilIcon, PlusIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useMutation } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { getCurrentInstance, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';
import Datatable from '../../../components/Datatable.vue';
import SearchInput from '../../../components/SearchInput.vue';
const columns = reactive([
    "Pertemuan",
    "Tanggal",
    "Kelas",
    "Mata Kuliah",
    "Kelas Tambahan",
    "Jumlah Kuis",
    "Actions"
])
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const requestDelete = async (id) => {
    return await http.delete(`/sesi/${id}`)
}

const router = useRouter()
const route = useRoute()
const update = ref(false)
const { mutate } = useMutation(requestDelete, {
    onSuccess() {
        toast.success("berhasil menghapus kelas", { position: "top-right" })
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
    return await http.post('sesi/reset')
}

const { mutate: reset, isLoading: loading_reset } = useMutation(reset_req, {
    onSuccess() {
        toast.success('Berhasil mereset semua data sesi', { position: "top-right" })
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

const filters = reactive<{ kelas?: string }>({})

const getKelas = async (search) => {
    return await http.get(search.length ? `kelas/all?search=${search}` : `kelas/all`)
}
function setKelas(val) {
    filters.kelas = val;
}


const batch_remove = async (checkeds) => {
    return await http.post('sesi/delete-selected', { checkeds })
}

const batch_actions = ref<string>("")
const { mutate: batch_remove_handle, status: batch_remove_status } = useMutation(batch_remove, {
    onSuccess() {
        toast.success('Berhasil menghapus data mahasiswa', { position: "top-right" })
        update.value = true
    }, onError() {
        toast.error('Terjadi kesalahan', { position: "top-right" })
    }, onSettled() {
        update.value = false
        batch_actions.value = ""
    },
})
const handleBatch = async (checkeds) => {
    if (batch_actions.value.length) {
        switch (batch_actions.value) {
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
            Management Sesi Pertemuan
        </h3>
        <div class="flex lg:flex-row items-start gap-3 flex-wrap">
            <router-link class="btn btn-neutral btn-sm" :to="{ name: 'create-sesi' }">
                <PlusIcon class="h-5 w-5" /> Add new
            </router-link>
            <button :disabled="loading_reset" @click.prevent="handleReset" class="btn btn-error btn-outline btn-sm">
                <XMarkIcon class="h-5 w-5" /> Reset Data
            </button>
        </div>
        <div class="w-full max-w-sm mt-3">
            <SearchInput :model="filters.kelas" @on-selected="setKelas" value="id" label="kelas" :request="getKelas"
                label-input="Filter berdasarkan Kelas" no-opt="Ketik untuk mencari Kelas" />
        </div>
        <Datatable url="sesi" :columns="columns" :update="update"
            :filter="{ ...filters.kelas != null && { kelas: (filters.kelas as any).label } }" checked="id" >
            <template #batchActions="data">
                <form class="flex max-w-sm w-full space-x-4" @submit.prevent="handleBatch(data.checkeds)">
                    <select class="select w-full select-bordered" v-model="batch_actions"
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
                    {{ data.data.pertemuan }}
                </td>
                <td>
                    {{ data.data.tanggal }}
                </td>
                <td>
                    {{ data.data.kelas }}
                </td>
                <td>
                    {{ data.data.makul }}
                </td>
                <td>
                    <span class="badge badge-success" v-if="Boolean(data.data.tambahan)">
                        Ya
                    </span>
                    <span class="badge badge-info" v-else>Tidak</span>
                </td>
                <td>
                    {{ data.data.kuis_count }}
                </td>
                <th class="flex flex-wrap gap-2">
                    <router-link :to="{ name: 'edit-sesi', params: { id: data.data.id } }"
                        class="btn btn-sm btn-neutral btn-outline">
                        <PencilIcon class="w-4 h-4" />
                    </router-link>
                    <button @click.prevent="handleDelete(data.data.id)" class="btn btn-sm btn-neutral btn-outline">
                        <TrashIcon class="w-4 h-4" />
                    </button>
                    <router-link :to="{ name: 'kuis', params: { sesi_id: data.data.id } }"
                        class="btn btn-sm btn-neutral btn-outline tooltip" data-tip="Lihat detail pertemuan">
                        <FolderIcon class="w-4 h-4 mt-2" />
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