import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import MovieCard from "../MovieCard";
import NavBar from "../NavBar";

export default function Home() {
  const { pageNumber } = useParams();
  const navigate = useNavigate();
  const [movies, setMovies] = useState([]);
  const [searchResults, setSearchResults] = useState([]);
  const page = parseInt(pageNumber, 10) || 1;

  useEffect(() => {
    const fetchMovies = async () => {
      if (searchResults.length === 0) {
        const response = await fetch(`https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/movies/?page=${page}`);
        const jsonResponse = await response.json();
        setMovies(jsonResponse);
      }
    };

    fetchMovies();
  }, [page, searchResults.length]);

  const displayedMovies = searchResults.length > 0 ? searchResults : movies;

  return (
    <>
      <header>
        <NavBar onSearchComplete={setSearchResults} searchEndpoint="https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/movies/" />
      </header>
      <main>
        <div className="movie-cards">
          {displayedMovies.map((movie, index) => (
            <MovieCard
              key={index}
              poster={movie.poster}
              title={movie.title}
              rating={movie.vote_average}
              movieID={movie.movieID}
            />
          ))}
        </div>
        <div className="pagination">
          <button
            onClick={() => navigate(`/home/page/${Math.max(page - 1, 1)}`)}
            disabled={page === 1}
          >
            Previous
          </button>
          <button onClick={() => navigate(`/home/page/${page + 1}`)}>Next</button>
        </div>
      </main>
    </>
  );
}
