import axios from "axios";
import { useForm } from "react-hook-form";
import { toast, ToastContainer } from "react-toastify";

const Add = () => {
    const { register, handleSubmit, formState: { errors }, reset } = useForm();

    const onSubmit = (data) => {
        axios.post("http://localhost:4000/api/category/addCategory.php", { data }).then(res => {
            if(res.data.success) {
                toast.success("Category added successfully!");
                reset(); // Reset the form after successful submission
            } else {
                toast.error("Failed to add category.");
            }
        });
    };

    return (
        <div className="p-4">
            <ToastContainer />
            <h2 className="text-3xl mb-3">Add Category</h2>
            <form onSubmit={handleSubmit(onSubmit)}>
                <input type="text" name="name" placeholder="Category Name" className="border rounded p-1 rounded-r-none w-64" {...register("name", { required: "Category name is required" })} />
                <button type="submit" className="border rounded rounded-l-none p-1 px-2 cursor-pointer hover:bg-blue-600 hover:text-white border-black">Add Category</button>
            </form>
            {errors.name && <p className="text-red-500 mt-1">{errors.name.message}</p>}
        </div>
    );
};

export default Add;