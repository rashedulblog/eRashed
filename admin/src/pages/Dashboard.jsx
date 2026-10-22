import axios from "axios";
import { useEffect, useState } from "react";
import { Link } from "react-router";

const Dashboard = () => {
    const [stats, setStats] = useState({
        total: 0,
        pending: 0,
        shift: 0,
        delivered: 0,
        cancel: 0,
        productsCount: 0,
        categoriesCount: 0,
        totalRevenue: 0,
    });
    const [recentOrders, setRecentOrders] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchDashboardData = async () => {
            try {
                const [ordersRes, productsRes, categoriesRes] = await Promise.allSettled([
                    axios.get("http://localhost:4000/api/order/getOrders.php"),
                    axios.get("http://localhost:4000/api/product/getProducts.php"),
                    axios.get("http://localhost:4000/api/category/getCategories.php"),
                ]);

                const ordersData = ordersRes.status === "fulfilled" && ordersRes.value.data?.success ? ordersRes.value.data.data : [];
                const productsData = productsRes.status === "fulfilled" && productsRes.value.data?.success ? productsRes.value.data.data : [];
                const categoriesData = categoriesRes.status === "fulfilled" && categoriesRes.value.data?.success ? categoriesRes.value.data.data : [];

                let pending = 0;
                let shift = 0;
                let delivered = 0;
                let cancel = 0;
                let revenue = 0;

                ordersData.forEach((o) => {
                    const st = (o.status || "").toLowerCase();
                    if (st === "pending" || st === "processing") pending++;
                    else if (st === "shift" || st === "shifted" || st === "shipped") shift++;
                    else if (st === "delivered") {
                        delivered++;
                        revenue += parseFloat(o.amount || 0);
                    } else if (st === "cancel" || st === "cancelled" || st === "failed" || st === "falied" || st === "canceled") {
                        cancel++;
                    }
                });

                setStats({
                    total: ordersData.length,
                    pending,
                    shift,
                    delivered,
                    cancel,
                    productsCount: productsData.length,
                    categoriesCount: categoriesData.length,
                    totalRevenue: revenue,
                });
                setRecentOrders(ordersData.slice(0, 5));
            } catch (error) {
                console.error("Dashboard fetch error:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchDashboardData();
    }, []);

    const cards = [
        {
            label: "Pending Orders",
            count: stats.pending,
            path: "/orders/pending",
            color: "bg-amber-500",
            textColor: "text-amber-600",
            bgColor: "bg-amber-50",
            borderColor: "border-amber-200",
        },
        {
            label: "Shifted Orders",
            count: stats.shift,
            path: "/orders/shift",
            color: "bg-blue-600",
            textColor: "text-blue-600",
            bgColor: "bg-blue-50",
            borderColor: "border-blue-200",
        },
        {
            label: "Delivered Orders",
            count: stats.delivered,
            path: "/orders/delivered",
            color: "bg-green-600",
            textColor: "text-green-600",
            bgColor: "bg-green-50",
            borderColor: "border-green-200",
        },
        {
            label: "Cancelled Orders",
            count: stats.cancel,
            path: "/orders/cancel",
            color: "bg-red-500",
            textColor: "text-red-600",
            bgColor: "bg-red-50",
            borderColor: "border-red-200",
        },
    ];

    return (
        <div className="p-6 bg-slate-50 min-h-screen">
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-slate-800">Admin Dashboard</h1>
                <p className="text-xs text-slate-500 mt-1">Overview of orders, products, and categories.</p>
            </div>

            {/* Metric Cards Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                {cards.map((c) => (
                    <Link
                        key={c.label}
                        to={c.path}
                        className={`p-5 rounded-2xl border ${c.borderColor} ${c.bgColor} shadow-sm hover:shadow-md transition flex flex-col justify-between`}
                    >
                        <div className="flex items-center justify-between mb-2">
                            <span className="text-xs font-bold uppercase tracking-wider text-slate-600">{c.label}</span>
                            <span className={`w-3 h-3 rounded-full ${c.color}`}></span>
                        </div>
                        <div className="flex items-baseline justify-between mt-2">
                            <span className={`text-3xl font-extrabold ${c.textColor}`}>{c.count}</span>
                            <span className="text-xs text-slate-500 font-medium hover:underline">View all &rarr;</span>
                        </div>
                    </Link>
                ))}
            </div>

            {/* Quick Summary Row */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <span className="text-xs font-bold uppercase text-slate-500">Total Products</span>
                    <div className="text-2xl font-bold text-slate-800 mt-1">{stats.productsCount}</div>
                    <Link to="/products" className="text-xs text-indigo-600 font-medium hover:underline mt-2 inline-block">Manage Products &rarr;</Link>
                </div>
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <span className="text-xs font-bold uppercase text-slate-500">Total Categories</span>
                    <div className="text-2xl font-bold text-slate-800 mt-1">{stats.categoriesCount}</div>
                    <Link to="/categories" className="text-xs text-indigo-600 font-medium hover:underline mt-2 inline-block">Manage Categories &rarr;</Link>
                </div>
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <span className="text-xs font-bold uppercase text-slate-500">Delivered Revenue</span>
                    <div className="text-2xl font-bold text-emerald-600 mt-1">BDT {Number(stats.totalRevenue).toLocaleString()}</div>
                    <span className="text-xs text-slate-400 mt-2 inline-block">From completed orders</span>
                </div>
            </div>

            {/* Recent Orders Section */}
            <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div className="flex items-center justify-between mb-4">
                    <h2 className="text-base font-bold text-slate-800">Recent Orders</h2>
                    <Link to="/orders/pending" className="text-xs text-indigo-600 font-semibold hover:underline">View Pending Orders &rarr;</Link>
                </div>

                {loading ? (
                    <div className="p-6 text-center text-slate-400 text-sm">Loading recent orders...</div>
                ) : recentOrders.length === 0 ? (
                    <div className="p-6 text-center text-slate-400 text-sm">No orders recorded yet.</div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs border-collapse">
                            <thead className="bg-slate-100 text-slate-700 font-semibold uppercase">
                                <tr>
                                    <th className="p-3">Order ID</th>
                                    <th className="p-3">Customer</th>
                                    <th className="p-3">Phone</th>
                                    <th className="p-3">Amount</th>
                                    <th className="p-3">Status</th>
                                    <th className="p-3">Date</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-200">
                                {recentOrders.map((ord) => (
                                    <tr key={ord.id} className="hover:bg-slate-50">
                                        <td className="p-3 font-semibold text-slate-800">#{ord.id}</td>
                                        <td className="p-3 font-medium text-slate-900">{ord.name || "N/A"}</td>
                                        <td className="p-3 text-slate-600">{ord.phone || "N/A"}</td>
                                        <td className="p-3 font-bold text-slate-800">BDT {Number(ord.amount).toLocaleString()}</td>
                                        <td className="p-3">
                                            <span className={`px-2 py-0.5 rounded-full text-xs font-semibold ${
                                                ord.status === "Delivered"
                                                    ? "bg-green-100 text-green-800"
                                                    : ord.status === "Pending" || ord.status === "Processing"
                                                    ? "bg-amber-100 text-amber-800"
                                                    : ord.status === "Shift"
                                                    ? "bg-blue-100 text-blue-800"
                                                    : "bg-red-100 text-red-800"
                                            }`}>
                                                {ord.status}
                                            </span>
                                        </td>
                                        <td className="p-3 text-slate-500">
                                            {ord.created_at ? new Date(ord.created_at).toLocaleDateString() : "N/A"}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
};

export default Dashboard;