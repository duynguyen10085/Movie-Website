import { useParams } from "react-router-dom";
import { useState, useEffect } from "react";
import NavBar from "../NavBar";
import AddtoList from "../AddtoList";
import "../../styles/Movie.css";

function Movie() {
  const { pathid } = useParams();
  const [movie, setMovie] = useState(null);
  const userID = 1;

  useEffect(() => {
    const fetchMovieDetails = async () => {
      const response = await fetch(`https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/movies/${pathid}`);
      const jsonResponse = await response.json();
      setMovie(jsonResponse);
    };
    fetchMovieDetails();
  }, [pathid]);

  if (!movie) {
    return <div>Loading...</div>;
  }

  return (
    <>
      <header> <NavBar/> </header>
      <main>
        <div className="movie-container">
          <img src={movie.poster} alt={movie.title} />
          <div className = "text-container">
              <h2>{movie.title}</h2>
              <p>Overview: {movie.overview}</p>
              <hr />
              <p>Language: {movie.original_language}</p>
              <hr />
              <p>Release Date: {movie.release_date}</p>
              <hr />
              <p>Runtime: {movie.runtime} minutes</p>
              <hr />
              <p>Tagline: {movie.tagline}</p>
              <hr />
              <p>⭐ {movie.vote_average} ({movie.vote_count} votes)</p>
              <div className="movie-button">
                  <a href={movie.homepage} target="_blank" rel="noopener noreferrer">Homepage</a>
                  <AddtoList movieID={pathid}/>
              </div>
          </div>
          
        </div>
      </main>
    </>
  );
}

export default Movie;
