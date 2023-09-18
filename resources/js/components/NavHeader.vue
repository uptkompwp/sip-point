
<script lang="ts" setup>
import { ArrowLeftOnRectangleIcon, Bars3Icon, UserIcon } from "@heroicons/vue/24/outline";
import { useMutation } from '@tanstack/vue-query';
import { getCurrentInstance } from 'vue';
import { useRouter } from 'vue-router';
import { useGlobalStore } from '../stores/globalStore';
import { useMenuStore } from '../stores/menuStore';
const menuStore = useMenuStore()
const globalStore = useGlobalStore()
const { auth } = getCurrentInstance()?.appContext.app as unknown as { auth: any }
const handleMenuClick = () => {
    menuStore.setShowMenu(!menuStore.getShowMenu);
}

const router = useRouter()
const logout_req = () => {
    return new Promise(async (resolve, reject) => {
        try {
            resolve(await auth.logout({
                makeRequest: true,
            }))
        } catch (e) {
            reject(e)
        }
    })
}

const { mutate } = useMutation(logout_req, {
    onSuccess() {
        router.replace({ name: "auth-login" })
        globalStore.setLoading(false)
    },
    onMutate() {
        globalStore.setLoading(true)
    },
    onSettled() {
        globalStore.setLoading(false)
    }
})


const handleLogout = async () => {
    await mutate()
}
</script>
<template>
    <nav
        class="p-[0.75rem] border-b bg-white mb-4 lg:grid flex justify-between lg:justify-normal items-center sticky -top-1 z-[777]">
        <button class="btn justify-self-start" v-if="menuStore.getIsMobile" @click.prevent="handleMenuClick">
            <Bars3Icon class="h-5 w-5" />
        </button>
        <div class="dropdown justify-self-end dropdown-bottom dropdown-end">
            <label tabindex="0" class="cursor-pointer m-1">
                <div class="avatar placeholder">
                    <div class="bg-neutral-focus text-neutral-content rounded-full w-12">
                        <span>UPT</span>
                    </div>
                </div>
            </label>
            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded px-4 w-52 space-y-5 mt-3"
                v-if="auth.check()">
                <li>
                    <router-link :to="{ name: 'profile' }" class="btn btn-neutral btn-outline btn-sm flex items-center">
                        <UserIcon class="h-4 w-4" /> <span class="text-xs truncate w-[100px]">{{ auth.user().nama }}</span>
                    </router-link>
                </li>
                <li>
                    <button class="btn btn-error btn-outline justify-center btn-sm flex items-center"
                        :disabled="globalStore.getLoading" @click.prevent="handleLogout">
                        <ArrowLeftOnRectangleIcon class="h-4 w-4" /> <span>Logout</span>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
</template>
<script lang="ts">
export default {
    name: "NavHeader",
    components: {
    }
}
</script>
<style lang="">
    
</style>