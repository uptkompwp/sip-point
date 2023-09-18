<script lang="ts" setup>
import { PencilIcon, PlusIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useMutation } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { getCurrentInstance, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';
import Datatable from '../../../components/Datatable.vue';
const columns = reactive([
    "Nama",
    "Username",
    "Actions"
])
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const requestDelete = async (id) => {
    return await http.delete(`/asisten/${id}`)
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
    return await http.post('asisten/reset')
}

const { mutate: reset, isLoading: loading_reset } = useMutation(reset_req, {
    onSuccess() {
        toast.success('Berhasil mereset semua data asisten', { position: "top-right" })
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


</script>
<template>
    <div class="p-3">
        <h3 class="text-lg mb-4 font-semibold">
            Management Asisten
        </h3>
        <div class="flex lg:flex-row items-start gap-3 flex-wrap">
            <router-link class="btn btn-neutral btn-sm" :to="{ name: 'create-asisten' }">
                <PlusIcon class="h-5 w-5" /> Add new
            </router-link>
            <button :disabled="loading_reset" @click.prevent="handleReset" class="btn btn-error btn-outline btn-sm">
                <XMarkIcon class="h-5 w-5" /> Reset Data
            </button>
        </div>
        <Datatable url="asisten" :columns="columns" :update="update">
            <!-- <template #batchActions="data">
                <div class="flex max-w-sm w-full space-x-4">
                    <select class="select w-full select-bordered" v-model="batch_actions">
                        <option value="">--Pilih Actions ({{ data.checkeds.length }})--</option>
                        <option value="Hapus">Hapus ({{ data.checkeds.length }})</option>
                    </select>
                    <button class="btn btn-neutral" type="submit" :disabled="!data.checkeds.length">Submit</button>
                </div>
            </template> -->
            <template #content="data">
                <td>
                    {{ data.data.nama }}
                </td>
                <td>
                    {{ data.data.username }}
                </td>
                <th class="flex flex-wrap gap-2">
                    <router-link :to="{ name: 'edit-asisten', params: { id: data.data.id } }"
                        class="btn btn-sm btn-neutral btn-outline">
                        <PencilIcon class="w-4 h-4" />
                    </router-link>
                    <button @click.prevent="handleDelete(data.data.id)" class="btn btn-sm btn-neutral btn-outline">
                        <TrashIcon class="w-4 h-4" />
                    </button>
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