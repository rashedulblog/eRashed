import axios from "axios";
import { useEffect, useState } from "react";
import DataTable from "react-data-table-component";
import { useNavigate } from "react-router";

const Products = () => {
    const [products, setProducts] = useState([]);
    const navigate = useNavigate();
    
    useEffect(() => {
        (async () => {
            try {
                const res = await axios.get("http://localhost:4000/api/product/getProducts.php");
                setProducts(res.data.data);
            } catch (error) {
                console.error("Failed to fetch products:", error);
            }
        })();
    }, []);

    const handleEdit = (id) => {
        navigate(`/products/edit/${id}`);
    };

    const handleDelete = async (id) => {
        if (window.confirm("Are you sure you want to delete this product?")) {
            try {
                await axios.delete(`http://localhost:4000/api/product/deleteProduct.php?id=${id}`);
                setProducts(products.filter(product => product.id !== id));
                alert("Product deleted successfully");
            } catch (error) {
                console.error("Failed to delete product:", error);
                alert("Failed to delete product");
            }
        }
    };

    const columns = [
        {
            name: "S/N",
            selector: (row, index) => index + 1,
        },
        {
            name: "image",
            selector: (row) => <img src={`http://localhost:4000/assets/images/products/${row.image}`} alt={row.name} className="w-16 h-16 object-cover" />,
        },
        {
            name: "Name",
            selector: (row) => row.name,
        },
        {
            name: "Regular Price",
            selector: (row) => row.regular_price,
        },
        {
            name: "Discount Price",
            selector: (row) => row.discount_price,
        },
        {
            name: "Action",
            selector: (row) => (
                <div className="flex gap-2">
                    <button
                        onClick={() => handleEdit(row.id)}
                        className="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm"
                    >
                        Edit
                    </button>
                    <button
                        onClick={() => handleDelete(row.id)}
                        className="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
                    >
                        Delete
                    </button>
                </div>
            ),
        }
    ];

    return (
        <div className="p-4">
            <h2 className="text-3xl mb-3">All Products</h2>
            <DataTable
                columns={columns}
                data={products}
                pagination
                paginationPerPage={5}
                paginationRowsPerPageOptions={[5, 10, 25, 50]}
                highlightOnHover
                pointerOnHover
                responsive
                striped
                fixedHeader
            />
        </div>
    );
};

export default Products;