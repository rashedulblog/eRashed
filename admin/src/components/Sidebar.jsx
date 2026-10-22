
import { NavLink } from "react-router";

const navigation = [
  {
    label: "Dashboard",
    path: "/",
  },
  {
    label: "Product",
    children: [
      { label: "All Product", path: "/products" },
      { label: "Add New", path: "/products/add" },
    ],
  },
  {
    label: "Category",
    children: [
      { label: "All Category", path: "/categories" },
      { label: "Add New", path: "/categories/add" },
    ],
  },
  {
    label: "Orders",
    children: [
      { label: "Pending", path: "/orders/pending" },
      { label: "Shift", path: "/orders/shift" },
      { label: "Delivered", path: "/orders/delivered" },
      { label: "Cancel", path: "/orders/cancel" },
    ],
  },
];

const Sidebar = () => {
  return (
    <aside className="h-full bg-slate-900 p-4 text-slate-100">
      <div className="mb-6 border-b border-slate-700 pb-4">
        <h2 className="text-xl font-semibold">Admin Panel</h2>
        <p className="mt-1 text-sm text-slate-400">Management dashboard</p>
      </div>

      <nav className="space-y-4">
        {navigation.map((item) => (
          <div key={item.label}>
            {item.path ? (
              <NavLink
                to={item.path}
                className={({ isActive }) =>
                  `block rounded-md px-3 py-2 text-sm font-medium transition ${
                    isActive ? "bg-slate-700 text-white" : "text-slate-300 hover:bg-slate-800 hover:text-white"
                  }`
                }
              >
                {item.label}
              </NavLink>
            ) : (
              <>
                <div className="px-3 py-2 text-sm font-semibold uppercase tracking-wide text-slate-400">
                  {item.label}
                </div>
                <div className="ml-3 space-y-1">
                  {item.children?.map((child) => (
                    <NavLink
                      key={child.label}
                      to={child.path}
                      className={({ isActive }) =>
                        `block rounded-md px-3 py-2 text-sm transition ${
                          isActive ? "bg-slate-700 text-white" : "text-slate-300 hover:bg-slate-800 hover:text-white"
                        }`
                      }
                    >
                      {child.label}
                    </NavLink>
                  ))}
                </div>
              </>
            )}
          </div>
        ))}
      </nav>
    </aside>
  );
};

export default Sidebar;