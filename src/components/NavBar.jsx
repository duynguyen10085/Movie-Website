import { NavLink } from "react-router-dom";
import "../styles/NavBar.css";
import SearchForm from "./SearchForm";

function NavBar({ onSearchComplete, searchEndpoint }) {
  return (
    <nav className="navbar">
      <h1>
        NEWFILMS <div className="yellow">.NET</div>
      </h1>
      <div className="links">
        <NavLink to="/">Home</NavLink>
        <NavLink to="/towatchlist/entries">Plan List</NavLink>
        <NavLink to="/completedwatchlist/entries">Watched List</NavLink>
      </div>
      <SearchForm onSearchComplete={onSearchComplete} searchEndpoint={searchEndpoint} />
    </nav>
  );
}

export default NavBar;
