import axios from "axios";
import { useEffect, useState, useMemo } from "react";
import DataTable from "react-data-table-component";
import { toast, ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

const API_BASE_URL = "http://localhost:4000/api";
const IMAGE_BASE_URL = "http://localhost:4000/assets/images/products";

const OrderList = ({ title, statusFilter }) => {
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState("");
    const [selectedOrder, setSelectedOrder] = useState(null);
    const [deleteModalId, setDeleteModalId] = useState(null);
    const [updatingId, setUpdatingId] = useState(null);

    const fetchOrders = async () => {
        try {
            setLoading(true);
            const queryParam = statusFilter ? `?status=${encodeURIComponent(statusFilter)}` : "";
            const res = await axios.get(`${API_BASE_URL}/order/getOrders.php${queryParam}`);
            if (res.data?.success && Array.isArray(res.data?.data)) {
                setOrders(res.data.data);
            } else {
                setOrders([]);
            }
        } catch (error) {
            console.error("Failed to fetch orders:", error);
            toast.error("Failed to load orders");
            setOrders([]);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchOrders();
    }, [statusFilter]);

    const handleStatusChange = async (orderId, newStatus) => {
        try {
            setUpdatingId(orderId);
            const res = await axios.post(`${API_BASE_URL}/order/updateOrderStatus.php`, {
                id: orderId,
                status: newStatus,
            });

            if (res.data?.success) {
                toast.success(res.data.message || `Order #${orderId} moved to ${newStatus}`);
                fetchOrders();
                if (selectedOrder && selectedOrder.id === orderId) {
                    setSelectedOrder((prev) => ({ ...prev, status: newStatus }));
                }
            } else {
                toast.error(res.data?.message || "Failed to update status");
            }
        } catch (error) {
            console.error("Failed to update status:", error);
            toast.error("Failed to update status");
        } finally {
            setUpdatingId(null);
        }
    };

    const handleDeleteOrder = async (orderId) => {
        try {
            const res = await axios.post(`${API_BASE_URL}/order/deleteOrder.php`, { id: orderId });
            if (res.data?.success) {
                toast.success(`Order #${orderId} deleted successfully`);
                setDeleteModalId(null);
                if (selectedOrder?.id === orderId) {
                    setSelectedOrder(null);
                }
                fetchOrders();
            } else {
                toast.error(res.data?.message || "Failed to delete order");
            }
        } catch (error) {
            console.error("Failed to delete order:", error);
            toast.error("Failed to delete order");
        }
    };

    const getStatusBadge = (status) => {
        const s = (status || "").toLowerCase();
        if (s === "pending" || s === "processing") {
            return (
                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-300">
                    {status}
                </span>
            );
        }
        if (s === "shift" || s === "shifted" || s === "shipped") {
            return (
                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-300">
                    {status}
                </span>
            );
        }
        if (s === "delivered") {
            return (
                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-300">
                    {status}
                </span>
            );
        }
        if (s === "cancel" || s === "cancelled" || s === "failed" || s === "falied" || s === "canceled") {
            return (
                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-300">
                    {status}
                </span>
            );
        }
        return (
            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-300">
                {status}
            </span>
        );
    };

    const filteredOrders = useMemo(() => {
        if (!searchTerm.trim()) return orders;
        const q = searchTerm.toLowerCase();
        return orders.filter((o) => {
            return (
                String(o.id).toLowerCase().includes(q) ||
                (o.name && o.name.toLowerCase().includes(q)) ||
                (o.email && o.email.toLowerCase().includes(q)) ||
                (o.phone && o.phone.toLowerCase().includes(q)) ||
                (o.transaction_id && o.transaction_id.toLowerCase().includes(q)) ||
                (o.shipping_name && o.shipping_name.toLowerCase().includes(q)) ||
                (o.shipping_city && o.shipping_city.toLowerCase().includes(q))
            );
        });
    }, [orders, searchTerm]);

    const columns = [
        {
            name: "S/N",
            selector: (row, index) => index + 1,
            width: "70px",
            sortable: true,
        },
        {
            name: "Order ID",
            selector: (row) => `#${row.id}`,
            width: "95px",
            sortable: true,
            cell: (row) => <span className="font-semibold text-slate-800">#{row.id}</span>,
        },
        {
            name: "Customer",
            selector: (row) => row.name,
            sortable: true,
            cell: (row) => (
                <div className="py-1">
                    <div className="font-medium text-slate-900">{row.name || "N/A"}</div>
                    <div className="text-xs text-slate-500">{row.phone}</div>
                    <div className="text-xs text-slate-400">{row.email}</div>
                </div>
            ),
        },
        {
            name: "Shipping Address",
            selector: (row) => row.shipping_city || row.address,
            cell: (row) => (
                <div className="text-xs text-slate-600 max-w-[200px] truncate" title={`${row.shipping_name || row.name}, ${row.shipping_street_address || row.address}, ${row.shipping_city || ""}`}>
                    <span className="font-semibold text-slate-700">{row.shipping_name || row.name}</span>
                    <br />
                    <span>{row.shipping_street_address || row.address}</span>
                    {row.shipping_city && <span>, {row.shipping_city}</span>}
                </div>
            ),
        },
        {
            name: "Amount",
            selector: (row) => parseFloat(row.amount || 0),
            sortable: true,
            width: "120px",
            cell: (row) => (
                <span className="font-bold text-slate-800">
                    {row.currency || "BDT"} {Number(row.amount).toLocaleString()}
                </span>
            ),
        },
        {
            name: "Items",
            selector: (row) => row.items_count || 0,
            width: "80px",
            center: true,
            cell: (row) => (
                <span className="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-semibold">
                    {row.items_count || (row.items ? row.items.length : 0)} pcs
                </span>
            ),
        },
        {
            name: "Status",
            selector: (row) => row.status,
            sortable: true,
            width: "120px",
            cell: (row) => getStatusBadge(row.status),
        },
        {
            name: "Date",
            selector: (row) => row.created_at,
            sortable: true,
            width: "150px",
            cell: (row) => (
                <span className="text-xs text-slate-500">
                    {row.created_at ? new Date(row.created_at).toLocaleString() : "N/A"}
                </span>
            ),
        },
        {
            name: "Action",
            width: "240px",
            cell: (row) => (
                <div className="flex items-center gap-2 py-1">
                    {/* View details button */}
                    <button
                        onClick={() => setSelectedOrder(row)}
                        className="bg-indigo-600 hover:bg-indigo-700 text-white px-2.5 py-1 rounded text-xs font-medium transition shadow-sm"
                        title="View Full Order Details"
                    >
                        View
                    </button>

                    {/* Change Status Dropdown */}
                    <select
                        value={row.status}
                        disabled={updatingId === row.id}
                        onChange={(e) => handleStatusChange(row.id, e.target.value)}
                        className="text-xs border border-slate-300 rounded px-2 py-1 bg-white text-slate-700 hover:border-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer"
                    >
                        <option value="Pending">Pending</option>
                        <option value="Shift">Shift</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancel">Cancel</option>
                    </select>

                    {/* Delete button */}
                    <button
                        onClick={() => setDeleteModalId(row.id)}
                        className="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs font-medium transition shadow-sm"
                        title="Delete Order"
                    >
                        Delete
                    </button>
                </div>
            ),
        },
    ];

    return (
        <div className="p-6 bg-slate-50 min-h-screen">
            <ToastContainer position="top-right" autoClose={3000} />

            {/* Top Bar / Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold text-slate-800 flex items-center gap-3">
                        {title}
                        <span className="text-sm font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {orders.length} {orders.length === 1 ? "Order" : "Orders"}
                        </span>
                    </h1>
                    <p className="text-xs text-slate-500 mt-1">
                        Arranged in latest serial order. Manage statuses and view detailed customer/shipping records.
                    </p>
                </div>

                <div className="flex items-center gap-3">
                    <div className="relative">
                        <input
                            type="text"
                            placeholder="Search orders (ID, Name, Phone)..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="w-64 md:w-80 text-sm pl-9 pr-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                        />
                        <svg
                            className="w-4 h-4 text-slate-400 absolute left-3 top-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>

                    <button
                        onClick={fetchOrders}
                        className="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5"
                        title="Refresh List"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>

            {/* Data Table Container */}
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <DataTable
                    columns={columns}
                    data={filteredOrders}
                    progressPending={loading}
                    pagination
                    paginationPerPage={10}
                    paginationRowsPerPageOptions={[10, 20, 30, 50, 100]}
                    highlightOnHover
                    pointerOnHover
                    responsive
                    striped
                    noDataComponent={
                        <div className="p-8 text-center text-slate-500">
                            <p className="text-base font-medium">No orders found in this category.</p>
                            <p className="text-xs text-slate-400 mt-1">New incoming orders will appear here automatically.</p>
                        </div>
                    }
                />
            </div>

            {/* Order Details Modal */}
            {selectedOrder && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs overflow-y-auto">
                    <div className="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-slate-200">
                        {/* Modal Header */}
                        <div className="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-900 text-white">
                            <div>
                                <h3 className="text-lg font-bold flex items-center gap-2">
                                    Order #{selectedOrder.id} Details
                                    {getStatusBadge(selectedOrder.status)}
                                </h3>
                                <p className="text-xs text-slate-300 mt-0.5">
                                    Transaction ID: <span className="font-mono">{selectedOrder.transaction_id || "N/A"}</span>
                                </p>
                            </div>
                            <button
                                onClick={() => setSelectedOrder(null)}
                                className="text-slate-400 hover:text-white p-1 rounded-lg transition"
                            >
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {/* Modal Body */}
                        <div className="p-6 overflow-y-auto space-y-6">
                            {/* Billing & Shipping Section */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {/* Customer / Billing Information */}
                                <div className="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    <h4 className="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <svg className="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Customer & Billing Details
                                    </h4>
                                    <div className="text-xs space-y-1.5 text-slate-600">
                                        <div><span className="font-semibold text-slate-700">Name:</span> {selectedOrder.name || "N/A"}</div>
                                        <div><span className="font-semibold text-slate-700">Email:</span> {selectedOrder.email || "N/A"}</div>
                                        <div><span className="font-semibold text-slate-700">Mobile:</span> {selectedOrder.phone || "N/A"}</div>
                                        <div><span className="font-semibold text-slate-700">Billing Address:</span> {selectedOrder.address || "N/A"}</div>
                                        {selectedOrder.ship_cus_city && (
                                            <div><span className="font-semibold text-slate-700">City / Postcode:</span> {selectedOrder.ship_cus_city} ({selectedOrder.ship_cus_postcode || ""})</div>
                                        )}
                                    </div>
                                </div>

                                {/* Shipping / Delivery Information */}
                                <div className="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                                    <h4 className="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <svg className="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Shipping & Delivery Details
                                    </h4>
                                    <div className="text-xs space-y-1.5 text-slate-600">
                                        <div><span className="font-semibold text-slate-700">Recipient Name:</span> {selectedOrder.shipping_name || selectedOrder.name || "N/A"}</div>
                                        <div><span className="font-semibold text-slate-700">Delivery Mobile:</span> {selectedOrder.shipping_mobile_number || selectedOrder.phone || "N/A"}</div>
                                        <div><span className="font-semibold text-slate-700">House / Apartment:</span> {selectedOrder.shipping_house_number || selectedOrder.ship_house_number || "N/A"}</div>
                                        <div><span className="font-semibold text-slate-700">Street Address:</span> {selectedOrder.shipping_street_address || selectedOrder.ship_street_address || selectedOrder.address || "N/A"}</div>
                                        <div><span className="font-semibold text-slate-700">City & ZIP Code:</span> {selectedOrder.shipping_city || selectedOrder.ship_cus_city || "N/A"} ({selectedOrder.shipping_zip_code || selectedOrder.ship_cus_postcode || ""})</div>
                                    </div>
                                </div>
                            </div>

                            {/* Ordered Items Table */}
                            <div>
                                <h4 className="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">
                                    Ordered Items ({selectedOrder.items?.length || 0})
                                </h4>
                                <div className="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                                    <table className="w-full text-left text-xs border-collapse">
                                        <thead className="bg-slate-100 text-slate-700 font-semibold uppercase text-[11px]">
                                            <tr>
                                                <th className="p-3">Product</th>
                                                <th className="p-3 text-center">Unit Price</th>
                                                <th className="p-3 text-center">Quantity</th>
                                                <th className="p-3 text-right">Item Total</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-200 text-slate-700">
                                            {selectedOrder.items && selectedOrder.items.length > 0 ? (
                                                selectedOrder.items.map((item, idx) => (
                                                    <tr key={idx} className="hover:bg-slate-50">
                                                        <td className="p-3 flex items-center gap-3">
                                                            {item.product_image ? (
                                                                <img
                                                                    src={`${IMAGE_BASE_URL}/${item.product_image}`}
                                                                    alt={item.product_name}
                                                                    className="w-10 h-10 object-cover rounded-lg border border-slate-200"
                                                                    onError={(e) => { e.target.style.display = 'none'; }}
                                                                />
                                                            ) : (
                                                                <div className="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center text-slate-400 font-bold">
                                                                    #
                                                                </div>
                                                            )}
                                                            <div>
                                                                <div className="font-semibold text-slate-800">{item.product_name || `Product ID: ${item.product_id}`}</div>
                                                                <div className="text-[11px] text-slate-400">ID: #{item.product_id}</div>
                                                            </div>
                                                        </td>
                                                        <td className="p-3 text-center">
                                                            BDT {item.product_price ? Number(item.product_price).toLocaleString() : (Number(item.item_total) / Number(item.quantity || 1)).toLocaleString()}
                                                        </td>
                                                        <td className="p-3 text-center font-bold">
                                                            x{item.quantity}
                                                        </td>
                                                        <td className="p-3 text-right font-bold text-slate-900">
                                                            BDT {Number(item.item_total).toLocaleString()}
                                                        </td>
                                                    </tr>
                                                ))
                                            ) : (
                                                <tr>
                                                    <td colSpan="4" className="p-4 text-center text-slate-400 italic">
                                                        No item records attached to this order.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                        <tfoot className="bg-slate-50 font-bold text-slate-900 border-t border-slate-200">
                                            <tr>
                                                <td colSpan="3" className="p-3 text-right">Grand Total:</td>
                                                <td className="p-3 text-right text-sm text-indigo-700">
                                                    {selectedOrder.currency || "BDT"} {Number(selectedOrder.amount).toLocaleString()}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            {/* Quick Status Updater */}
                            <div className="bg-slate-100 p-4 rounded-xl border border-slate-200 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <span className="text-xs font-semibold text-slate-700">Update Order Status:</span>
                                    <span className="text-xs text-slate-500 block">Move this order to another stage directly</span>
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    {["Pending", "Shift", "Delivered", "Cancel"].map((st) => (
                                        <button
                                            key={st}
                                            disabled={selectedOrder.status === st || updatingId === selectedOrder.id}
                                            onClick={() => handleStatusChange(selectedOrder.id, st)}
                                            className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-xs ${
                                                selectedOrder.status === st
                                                    ? "bg-slate-300 text-slate-500 cursor-not-allowed"
                                                    : st === "Pending"
                                                    ? "bg-amber-500 hover:bg-amber-600 text-white"
                                                    : st === "Shift"
                                                    ? "bg-blue-600 hover:bg-blue-700 text-white"
                                                    : st === "Delivered"
                                                    ? "bg-green-600 hover:bg-green-700 text-white"
                                                    : "bg-red-500 hover:bg-red-600 text-white"
                                            }`}
                                        >
                                            {st}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Modal Footer */}
                        <div className="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
                            <button
                                onClick={() => setSelectedOrder(null)}
                                className="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-xl text-xs font-semibold transition"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Delete Confirmation Modal */}
            {deleteModalId && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
                    <div className="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center border border-slate-200">
                        <div className="w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-3">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <h3 className="text-lg font-bold text-slate-800 mb-1">Confirm Delete</h3>
                        <p className="text-xs text-slate-500 mb-5">
                            Are you sure you want to delete Order #{deleteModalId}? This will remove associated shipping and item records.
                        </p>
                        <div className="flex justify-center gap-3">
                            <button
                                onClick={() => setDeleteModalId(null)}
                                className="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-xl text-xs font-semibold transition"
                            >
                                Cancel
                            </button>
                            <button
                                onClick={() => handleDeleteOrder(deleteModalId)}
                                className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition shadow-sm"
                            >
                                Yes, Delete
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default OrderList;
