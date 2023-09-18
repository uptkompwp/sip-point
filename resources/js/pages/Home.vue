<script lang="ts" setup>
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }
const get_info = async () => {
    return await http.get('info');
}
const { status, data } = useQuery({
    queryKey: ["info"],
    queryFn: get_info
})
</script>
<template>
    <div class="grid gap-y-3">
        <h3 class="font-bold">
            Selamat datang di point management
        </h3>
        <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-4" v-if="status === 'success'">
            <Info :icon="PencilIcon" text="Total Data Pertemuan" :count="data.data.sesi">
                <template #icon>
                    <ClockIcon class="h-12 w-12" />
                </template>
            </Info>
            <Info :icon="PencilIcon" text="Total Data Mahasiswa" :count="data.data.mahasiswa">
                <template #icon>
                    <UserGroupIcon class="h-12 w-12" />
                </template>
            </Info>
            <Info :icon="PencilIcon" text="Total Data Kelas" :count="data.data.kelas">
                <template #icon>
                    <HomeIcon class="h-12 w-12" />
                </template>
            </Info>
            <Info :icon="PencilIcon" text="Total Data Mata Kuliah" :count="data.data.makul">
                <template #icon>
                    <PencilIcon class="h-12 w-12" />
                </template>
            </Info>
        </div>
        <Loading v-if="status === 'loading'" />
    </div>
</template>
<script lang="ts">
import { ClockIcon, HomeIcon, PencilIcon, UserGroupIcon } from '@heroicons/vue/24/outline';
import Datatable from '../components/Datatable.vue';
import Info from '../components/Info.vue';
import { getCurrentInstance } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import Loading from '../components/Loading.vue';

export default {
    name: "Home",
    components: { Datatable, Info, PencilIcon }
}
</script>
<style lang="">
    
</style>