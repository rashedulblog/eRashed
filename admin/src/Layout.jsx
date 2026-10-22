import { Outlet } from "react-router";
import Header from "./components/Header";
import Sidebar from "./components/Sidebar";

const Layout = () => {
    return (
        <div className="flex">
            <div className="w-1/5 bg-gray-200 h-screen">
                <Sidebar />
            </div>
            <div className="w-4/5">
                <Header />
                <Outlet />
            </div>
        </div>
    );
};

export default Layout;