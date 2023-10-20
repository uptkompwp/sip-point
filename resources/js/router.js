import { createRouter, createWebHistory } from "vue-router";
import { titleFormat } from "./helpers/title_format";
import Home from "./pages/Home.vue";
import Profile from "./pages/Profile/Index.vue";
import ChangePassword from "./pages/Profile/Change.vue";
import Login from "./pages/Auth/Login.vue";
/**
 * ERROR IMPORTS
 */
import NotFound from "./pages/Errors/NotFound.vue";
import Forbidden from "./pages/Errors/Forbidden.vue";
/**
 * MASTER IMPORTS
 */
// KELAS IMPORT
import Kelas from "./pages/Masters/Kelas/Index.vue";
import CreateKelas from "./pages/Masters/Kelas/Create.vue";
import EditKelas from "./pages/Masters/Kelas/Edit.vue";
// MAKUL IMPORT
import Makul from "./pages/Masters/Makul/Index.vue";
import CreateMakul from "./pages/Masters/Makul/Create.vue";
import EditMakul from "./pages/Masters/Makul/Edit.vue";
// MAHASISWA IMPORT
import Mahasiswa from "./pages/Masters/Mahasiswa/Index.vue";
import CreateMahasiswa from "./pages/Masters/Mahasiswa/Create.vue";
import EditMahasiswa from "./pages/Masters/Mahasiswa/Edit.vue";
import ImportMahasiswa from "./pages/Masters/Mahasiswa/Import.vue";
import HistoryMahasiswa from "./pages/Masters/Mahasiswa/History.vue";
// ASISTEN IMPORT
import Asisten from "./pages/Masters/Asisten/Index.vue";
import CreateAsisten from "./pages/Masters/Asisten/Create.vue";
import EditAsisten from "./pages/Masters/Asisten/Edit.vue";
/**
 * POINTS IMPORTS
 */
// SESI IMPORT
import Sesi from "./pages/Points/Sesi/Index.vue";
import CreateSesi from "./pages/Points/Sesi/Create.vue";
import EditSesi from "./pages/Points/Sesi/Edit.vue";
// KUIS IMPORT
import Kuis from "./pages/Points/Kuis/Index.vue";
import CreateKuis from "./pages/Points/Kuis/Create.vue";
import EditKuis from "./pages/Points/Kuis/Edit.vue";
// POINT IMPORT
import ManagePoint from "./pages/Points/Manage/Index.vue";
import CreatePoint from "./pages/Points/Manage/Create.vue";
import ImportPoint from "./pages/Points/Manage/Import.vue";
/**
 * REPORTS ROUTES
 */
import Laporan from "./pages/Laporan/Index.vue";
const routes = [
    {
        path: "/",
        component: Home,
        name: "home",
        meta: {
            auth: true,
            title: "Dashboard",
        },
    },
    {
        path: "/profile",
        component: Profile,
        name: "profile",
        meta: {
            auth: true,
            title: "Profile",
        },
        children: [
            {
                path: "change-password",
                component: ChangePassword,
                name: "profile-change-password",
                meta: {
                    auth: true,
                    title: "Change Password",
                },
            },
        ],
    },
    /**
     * POINT ROUTES
     */
    // manage sesi
    {
        path: "/point-management/sesi",
        component: Sesi,
        name: "sesi",
        meta: {
            auth: true,
            title: "Sesi",
        },
    },
    {
        path: "/point-management/sesi/create",
        component: CreateSesi,
        name: "create-sesi",
        meta: {
            auth: true,
            title: "Create Sesi",
        },
    },
    {
        path: "/point-management/sesi/:id/edit",
        component: EditSesi,
        name: "edit-sesi",
        meta: {
            auth: true,
            title: "Edit Sesi",
        },
    },
    // end manage sesi
    // manage kuis
    {
        path: "/point-management/sesi/:sesi_id/kuis",
        component: Kuis,
        name: "kuis",
        meta: {
            auth: true,
            title: "Kuis",
        },
    },
    {
        path: "/point-management/sesi/:sesi_id/kuis/create",
        component: CreateKuis,
        name: "create-kuis",
        meta: {
            auth: true,
            title: "Create Kuis",
        },
    },
    {
        path: "/point-management/sesi/:sesi_id/kuis/:id/edit",
        component: EditKuis,
        name: "edit-kuis",
        meta: {
            auth: true,
            title: "Edit Kuis",
        },
    },
    {
        path: "/point-management/kuis/:id/detail",
        component: ManagePoint,
        name: "detail-kuis",
        meta: {
            auth: true,
            title: "Detail Kuis",
        },
    },
    {
        path: "/point-management/kuis/:id/:sesi_id/detail/create-point",
        component: CreatePoint,
        name: "create-point",
        meta: {
            auth: true,
            title: "Create Point",
        },
    },
    {
        path: "/point-management/kuis/:id/detail/create-point/import",
        component: ImportPoint,
        name: "import-point",
        meta: {
            auth: true,
            title: "Import Point",
        },
    },
    {
        path: "/reports",
        component: Laporan,
        name: "reports",
        meta: { auth: true, title: "Reports" },
    },
    /**
     * MASTER ROUTES
     */
    // manage kelas routes
    {
        path: "/manage/kelas",
        component: Kelas,
        name: "kelas",
        meta: { auth: true, title: "Kelas" },
    },
    {
        path: "/manage/kelas/create",
        name: "create-kelas",
        component: CreateKelas,
        meta: { auth: true, title: "Create Kelas" },
    },
    {
        path: "/manage/kelas/:id/edit",
        name: "edit-kelas",
        component: EditKelas,
        meta: { auth: true, title: "Edit Kelas" },
    },
    // end routes kelas
    // manage makul routes
    {
        path: "/manage/mata-kuliah",
        component: Makul,
        name: "makul",
        meta: { auth: true, title: "Mata Kuliah" },
    },
    {
        path: "/manage/mata-kuliah/create",
        component: CreateMakul,
        name: "create-makul",
        meta: { auth: true, title: "Create Mata Kuliah" },
    },
    {
        path: "/manage/mata-kuliah/:id/edit",
        component: EditMakul,
        name: "edit-makul",
        meta: { auth: true, title: "Edit Mata Kuliah" },
    },
    // end routes makul
    // mahasiswa routes
    {
        path: "/manage/mahasiswa",
        component: Mahasiswa,
        name: "mahasiswa",
        meta: { auth: true, title: "Mahasiswa" },
    },
    {
        path: "/manage/mahasiswa/create",
        component: CreateMahasiswa,
        name: "create-mahasiswa",
        meta: { auth: true, title: "Add Mahasiswa" },
    },
    {
        path: "/manage/mahasiswa/:id/edit",
        component: EditMahasiswa,
        name: "edit-mahasiswa",
        meta: { auth: true, title: "Edit Mahasiswa" },
    },
    {
        path: "/manage/mahasiswa/:id/history",
        component: HistoryMahasiswa,
        name: "history-mahasiswa",
        meta: { auth: true, title: "History Mahasiswa" },
    },
    {
        path: "/manage/mahasiswa/import",
        component: ImportMahasiswa,
        name: "import-mahasiswa",
        meta: { auth: true, title: "Import Mahasiswa" },
    },
    // end mahasiswa routes
    // asisten routes
    // manage kelas routes
    {
        path: "/manage/asisten",
        component: Asisten,
        name: "asisten",
        meta: {
            auth: {
                roles: "ADMIN",
            },
            title: "Asisten",
        },
    },
    {
        path: "/manage/asisten/create",
        component: CreateAsisten,
        name: "create-asisten",
        meta: {
            auth: {
                roles: "ADMIN",
            },
            title: "Add Asisten",
        },
    },
    {
        path: "/manage/asisten/:id/edit",
        component: EditAsisten,
        name: "edit-asisten",
        meta: {
            auth: {
                roles: "ADMIN",
            },
            title: "Edit Asisten",
        },
    },
    // end asisten routes
    // auth routes
    {
        path: "/auth/login",
        component: Login,
        name: "auth-login",
        meta: { auth: false, title: "Login" },
    },
    /**
     * ERROR ROUTES
     */
    {
        path: "/404",
        component: NotFound,
        name: "not-found",
        meta: { title: "Not Found", auth: true },
    },
    {
        path: "/403",
        component: Forbidden,
        name: "forbidden",
        meta: { title: "Forbidden", auth: true },
    },
    // error routes
    {
        path: "/:pathMatch(.*)*",
        component: NotFound,
        name: "not-found",
        meta: { title: "Not Found", auth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((route) => {
    document.title = route.meta.title ? titleFormat(route.meta.title) : "SIP";
});
export default (app) => {
    app.router = router;
    app.use(router);
};
