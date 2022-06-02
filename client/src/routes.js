// @material-ui/icons
import Dashboard from "@material-ui/icons/Dashboard";
import Person from "@material-ui/icons/Person";
import DataUsageIcon from '@material-ui/icons/DataUsage';
import AccessTimeIcon from '@material-ui/icons/AccessTime';
import HighQualityIcon from '@material-ui/icons/HighQuality';
import LocalLibraryIcon from '@material-ui/icons/LocalLibrary';
import QuestionAnswerIcon from '@material-ui/icons/QuestionAnswer';
import NoteIcon from '@material-ui/icons/Note';
import GroupIcon from '@material-ui/icons/Group';
import DashboardPage from "views/Dashboard/Dashboard.js";
import UserProfile from "views/UserProfile/UserProfile.js";
import TableList from "views/TableList/TableList.js";
import Typography from "views/Typography/Typography.js";
import Indicators from "./views/UserProfile/Indicators/Indicators";
import ActiveTime from "./views/UserProfile/ActiveTime/ActiveTime";
import QualityControl from "./views/UserProfile/QualityControl/QualityControl";
import Dialogs from "./views/UserProfile/Dialogs/Dialogs";
import GroupDialogs from "./views/UserProfile/GroupDialogs/GroupDialogs";
import Knowledge from "./views/UserProfile/Knowledge/Knowledge";

const dashboardRoutes = [
    {
        path: "/dashboard",
        name: "Дашборд",
        icon: Dashboard,
        component: DashboardPage,
        layout: "",
        display: true
    },
    {
        path: "/user/:userId/:tabName?",
        default: {tabName: "indicators", userId: "me", },
        name: "Профиль",
        icon: Person,
        component: UserProfile,
        tabs: [
            {
                name: "Показатели",
                path: "indicators",
                icon: <DataUsageIcon/>,
                component: Indicators
            },
            {
                name: "Active Time",
                path: "at",
                icon: <AccessTimeIcon/>,
                component: ActiveTime,
            },
            {
                name: "Обучение",
                path: "knowledge",
                icon: <LocalLibraryIcon/>,
                component: Knowledge,
            },
            {
                name: "Диалоги",
                path: "dialogs",
                icon: <QuestionAnswerIcon/>,
                component: Dialogs
            },
            {
                name: "Диалоги группы",
                path: "group-dialogs",
                icon: <GroupIcon/>,
                component: GroupDialogs,
                role: "ROLE_SUPERVISOR"
            },
            {
                name: "Заявки",
                path: "orders",
                icon: <NoteIcon/>,
                component: ActiveTime,
                disabled:true
            },
        ],
        layout: "",
        display: false
    },
    {
        path: "/typography",
        name: "Сотрудники",
        icon: GroupIcon,
        component: Typography,
        layout: "",
        display: false
    },
    {
        path: "/table",
        name: "База знаний",
        icon: "content_paste",
        component: TableList,
        layout: "",
        display: false
    },
];

export default dashboardRoutes;
