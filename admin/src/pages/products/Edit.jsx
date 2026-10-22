import axios from "axios";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useParams } from "react-router";
import { toast, ToastContainer } from "react-toastify";


const Edit = () => {
    const { id } = useParams();
    const [product, setProduct] = useState();
    const [cat, setCat] = useState([]);

    const fetchProduct = async () => {
        try {
            const res = await axios.get(`http://localhost:4000/api/product/getSingleProduct.php?id=${id}`);
            setProduct(res.data.data);
        } catch (error) {
            console.error("Failed to fetch products:", error);
        }
    };


    const { register, handleSubmit, formState: { errors }, reset, watch } = useForm();

    useEffect(() => {
        fetchProduct();
    }, [id]);

    useEffect(() => {
        axios.get("http://localhost:4000/api/category/getCategories.php").then((res) => {
            setCat(res.data.data);
        });
    }, []);

    useEffect(() => {
        if (product && cat.length > 0) {
            reset({
                name: product.name,
                regular_price: product.regular_price,
                discount_price: product.discount_price,
                category_id: String(product.category_id),
            });
        }
    }, [product, cat, reset]);

    const onSubmit = async (data) => {
        const formData = new FormData();
        formData.append("id", id);
        formData.append("name", data.name);
        formData.append("regular_price", data.regular_price);
        formData.append("discount_price", data.discount_price || "");
        formData.append("category_id", data.category_id);

        try {
            const res = await axios.post(
                "http://localhost:4000/api/product/editProduct.php",
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );

            if (res.data.success) {
                toast.success(res.data.message);
                // Refetch the updated product data
                await fetchProduct();
            } else {
                throw new Error(res.data.message);
            }
        } catch (error) {
            toast.error(error.message || "Failed to update product. Please try again.");
        }
    };

    return (
        <div className="p-4">
            <ToastContainer />
            <h2 className="text-3xl mb-3">Edit Product</h2>
            <div className="flex gap-4">
                <div>
                    <form onSubmit={handleSubmit(onSubmit)} encType="multipart/form-data" className="w-max">
                        <input type="text" placeholder="Product Name" className="border rounded p-1 rounded-r-none w-64" {...register("name", { required: "Product name is required" })} />
                        <div className="mb-3 text-red-600 text-sm">
                            {errors.name && errors.name.message}
                        </div>
                        <input type="number" placeholder="Regular Price" className="border rounded p-1 rounded-r-none w-64 block mb-3" {...register("regular_price", { required: "Regular price is required" })} />
                        <div className="mb-3 text-red-600 text-sm">
                            {errors.regular_price && errors.regular_price.message}
                        </div>
                        <input type="number" placeholder="Discount Price" className="border rounded p-1 rounded-r-none w-64 block mb-3" {...register("discount_price", { validate: (value) => value >= 0 || "Discount price must be a positive number" })} />
                        <div className="mb-3 text-red-600 text-sm">
                            {errors.discount_price && errors.discount_price.message}
                        </div>
                        <select className="border rounded p-1 rounded-r-none w-64 block mb-3" {...register("category_id", { required: "Category is required" })}>
                            <option value="">Select Category</option>
                            {cat?.map((category) => (
                                <option key={category.id} value={category.id}>
                                    {category.name}
                                </option>
                            ))}
                        </select>
                        <div className="mb-3 text-red-600 text-sm">
                            {errors.category_id && errors.category_id.message}
                        </div>
                        <button type="submit" className="border rounded rounded-l-none p-1 px-2 cursor-pointer hover:bg-blue-600 hover:text-white border-black">Update Product</button>
                    </form>
                </div>
                <div>
                    <form action="" className="flex flex-col justify-start items-start gap-3">
                        <img src={`http://localhost:4000/assets/images/products/${product?.image}`} alt="" className="h-50" />
                        <input type="file" />
                        <button type="submit" className="border rounded rounded-l-none p-1 px-2 cursor-pointer hover:bg-blue-600 hover:text-white border-black">Upload new image</button>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default Edit;