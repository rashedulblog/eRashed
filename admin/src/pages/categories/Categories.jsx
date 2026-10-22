import axios from "axios";
import { useEffect, useState } from "react";
import { Link } from "react-router";
import { toast, ToastContainer } from "react-toastify";

const Categories = () => {
    const [categories, setCategories] = useState([]);
    const [categoriesPerPage] = useState(5);
    const [currentPage, setCurrentPage] = useState(1);
    const [delId, setDelId] = useState(null);

    const handleDelete = (id) => {
        axios.post("http://localhost:4000/api/category/deleteCategory.php", { id }).then((res) => {
            if (res.data.success) {
                toast.success("Category deleted successfully!");
                setDelId(null);
            } else {
                toast.error("Failed to delete category.");
            }
        });
    };

    useEffect(() => {
        axios
            .get("http://localhost:4000/api/category/getCategories.php")
            .then((res) => {
                const data = res?.data;
                if (Array.isArray(data)) {
                    setCategories(data);
                } else if (data && Array.isArray(data.data)) {
                    setCategories(data.data);
                } else if (typeof data === "string") {
                    try {
                        const parsed = JSON.parse(data);
                        setCategories(
                            Array.isArray(parsed)
                                ? parsed
                                : Array.isArray(parsed.data)
                                ? parsed.data
                                : []
                        );
                    } catch (e) {
                        console.warn("Could not parse categories response", e);
                        setCategories([]);
                    }
                } else {
                    setCategories([]);
                }
            })
            .catch((err) => {
                console.warn("Failed to fetch categories", err);
                setCategories([]);
            });
    }, [delId]);

    const total = Array.isArray(categories) ? categories.length : 0;
    const totalPages = Math.max(1, Math.ceil(total / categoriesPerPage));
    const page = Math.min(currentPage, totalPages);

    const startIndex = (page - 1) * categoriesPerPage;
    const endIndex = startIndex + categoriesPerPage;
    const currentCategories = Array.isArray(categories) ? categories.slice(startIndex, endIndex) : [];

    return (
        <div className="p-4">
            <ToastContainer />
            <h2 className="text-3xl mb-3">Categories Page</h2>
            <table className="border-collapse border border-black w-full">
                <thead>
                    <tr className="bg-gray-200 text-left">
                        <th className="border border-black p-2">SN</th>
                        <th className="border border-black p-2">Category Name</th>
                        <th className="border border-black p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {currentCategories?.map((category, index) => (
                        <tr key={category.id}>
                            <td className="border border-black p-2">{(page - 1) * categoriesPerPage + index + 1}</td>
                            <td className="border border-black p-2">{category.name}</td>
                            <td className="border border-black p-2">
                                <Link to={`/categories/edit/${category.id}`} className="bg-blue-500 text-white px-2 py-1 rounded mr-2">Edit</Link>
                                <button className="bg-red-500 text-white px-2 py-1 rounded" onClick={() => setDelId(category.id)}>
                                    Delete
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <div className="mt-4">
                <button
                    className="bg-gray-300 px-2 py-1 rounded mr-2"
                    onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}
                    disabled={page === 1}
                >
                    Previous
                </button>
                <span className="mx-2">Page {page} of {totalPages}</span>
                <button
                    className="bg-gray-300 px-2 py-1 rounded"
                    onClick={() => setCurrentPage((prev) => Math.min(prev + 1, totalPages))}
                    disabled={page === totalPages}
                >
                    Next
                </button>
            </div>
            {/* Modal for delete confirmation */}
            {delId && (
                <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div className="bg-white p-6 rounded shadow-lg">
                        <h3 className="text-lg font-bold mb-4">Confirm Delete</h3>
                        <p className="mb-4">Are you sure you want to delete this category?</p>
                        <div className="flex justify-end space-x-2">
                            <button
                                className="bg-gray-500 text-white px-4 py-2 rounded"
                                onClick={() => setDelId(null)}
                            >
                                Cancel
                            </button>
                            <button
                                className="bg-red-500 text-white px-4 py-2 rounded"
                                onClick={() => handleDelete(delId)}
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Categories;