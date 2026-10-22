import axios from "axios";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { toast, ToastContainer } from "react-toastify";

const Add = () => {
    const [cat, setCat] = useState([]);

    const { register, handleSubmit, formState: { errors }, reset } = useForm();

    const onSubmit = async (data) => {
        const formData = new FormData();

        formData.append("name", data.name);
        formData.append("regular_price", data.regular_price);
        formData.append("discount_price", data.discount_price || "");
        formData.append("category_id", data.category_id);
        formData.append("image", data.image[0]); // first selected file

        try {
            const res = await axios.post(
                "http://localhost:4000/api/product/addProduct.php",
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );

            if (res.data.success) {
                toast.success(res.data.message);
            }else {
                throw new Error(res.data.message);
            }
            reset();
        } catch (error) {
            toast.error(error.message || "Failed to add product. Please try again.");
        }
    };

    useEffect(() => {
        axios.get("http://localhost:4000/api/category/getCategories.php").then((res) => {
            setCat(res.data.data);
        });
    }, []);

    return (
        <div className="p-4">
            <ToastContainer />
            <h2 className="text-3xl mb-3">Add Product</h2>
            {/* `name`, `regular_price`, `discount_price`, `category_id`, `image`, `created_at` */}
            <form onSubmit={handleSubmit(onSubmit)} encType="multipart/form-data" className="">
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
                <input type="file" className="border rounded p-1 rounded-r-none w-64 block mb-3" {...register("image", { required: "Image is required" })} />
                <div className="mb-3 text-red-600 text-sm">
                    {errors.image && errors.image.message}
                </div>
                <button type="submit" className="border rounded rounded-l-none p-1 px-2 cursor-pointer hover:bg-blue-600 hover:text-white border-black">Add Product</button>
            </form>
        </div>
    );
};

export default Add;