import { Link } from "react-router-dom";
import "../styles/MovieCard.css"; // Ensure the path to your CSS file is correct

function MovieCard({ poster, title, rating, movieID, priority, className }) {
  return (
    <article className={`movieCard ${className}`}>
      <div className="priority">{priority}. </div>
      <img src={poster} alt={title} /> 
      <Link to={`/movies/${movieID}`} className="movieLink">
        <div className="movieInfo">
          <h2>{title}</h2>
          <p>⭐ {rating}</p>
        </div>
      </Link>
    </article>
  );
}

export default MovieCard;
