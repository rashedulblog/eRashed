import axios from "axios";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { Link, useParams } from "react-router";
import { toast, ToastContainer } from "react-toastify";

const Edit = () => {
    const [preCat, setPreCat] = useState(null);
    const { id } = useParams();


    const { register, handleSubmit, formState: { errors }, reset } = useForm();

    useEffect(() => {
        axios.get(`http://localhost:4000/api/category/getSingleCategory.php?id=${id}`).then((res) => {
            setPreCat(res.data);
        });
    }, [id, preCat, reset]);

    const onSubmit = (data) => {
        axios.post("http://localhost:4000/api/category/updateCategory.php", { id, data }).then(res => {
            if(res.data.success) {
                toast.success("Category updated successfully!");
                reset(); // Reset the form after successful submission
            } else {
                toast.error("Failed to update category.");
            }
        });
    };

    return (
        <div className="p-4">
            <ToastContainer />
            <h2 className="text-3xl mb-3">Edit Category</h2>
            <form onSubmit={handleSubmit(onSubmit)}>
                <input type="text" name="name" placeholder="Category Name" className="border rounded p-1 rounded-r-none w-64" {...register("name", { required: "Category name is required" })} defaultValue={preCat?.data?.name || ""} />
                <button type="submit" className="border rounded rounded-l-none p-1 px-2 cursor-pointer hover:bg-blue-600 hover:text-white border-black">Update Category</button>
            </form>
            {errors.name && <p className="text-red-500 mt-1">{errors.name.message}</p>}
            <Link to="/categories" className="mt-4 inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Categories</Link>
        </div>
    );
};

export default Edit;