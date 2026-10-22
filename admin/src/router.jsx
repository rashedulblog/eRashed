import { createBrowserRouter } from "react-router";
import Layout from "./Layout";
import Dashboard from "./pages/Dashboard";
import Products from "./pages/products/Products";
import AddProduct from "./pages/products/Add";
import EditProduct from "./pages/products/Edit";
import Categories from "./pages/categories/Categories";
import AddCategory from "./pages/categories/Add";
import Pending from "./pages/orders/Pending";
import Shift from "./pages/orders/Shift";
import Delivered from "./pages/orders/Delivered";
import Cancel from "./pages/orders/Cancel";
import Edit from "./pages/categories/Edit";

const router = createBrowserRouter([
  {
    path: "/",
    element: <Layout />,
    children: [
      {
        path: "/",
        element: <Dashboard />,
      },
      {
        path: "/products",
        element: <Products />,
      },
      {
        path: "/products/add",
        element: <AddProduct />,
      },
      {
        path: "/products/edit/:id",
        element: <EditProduct />
      },
      {
        path: "/categories",
        element: <Categories />,
      },
      {
        path: "/categories/add",
        element: <AddCategory />,
      },
      {
        path: "/categories/edit/:id",
        element: <Edit />,
      },
      {
        path: "/orders/pending",
        element: <Pending />,
      },
      {
        path: "/orders/shift",
        element: <Shift />,
      },
      {
        path: "/orders/delivered",
        element: <Delivered />,
      },
      {
        path: "/orders/cancel",
        element: <Cancel />,
      },
    ],
  },
]);

export default router;