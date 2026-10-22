
const Header = () => {
    return (
        <header className="text-gray-600 body-font bg-stone-200">
            <div className="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                <nav className="md:ml-auto flex flex-wrap items-center text-base justify-center">
                    <a className="mr-5 hover:text-gray-900">First Link</a>
                    <a className="mr-5 hover:text-gray-900">Second Link</a>
                    <a className="mr-5 hover:text-gray-900">Third Link</a>
                    <a className="mr-5 hover:text-gray-900">Fourth Link</a>
                </nav>
            </div>
        </header>
    );
};

export default Header;