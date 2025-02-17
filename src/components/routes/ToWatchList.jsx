import React, { useState, useEffect } from "react";
import NavBar from "../NavBar";
import AddtoList from "../AddtoList";
import "../../styles/toWatchList.css";
import UpdateMoviePriority from "../UpdatePriority";
import AddtoCompleteList from "../AddtoCompleteList";
import Rating from "../Rating";

export default function ToWatchList() {
  const [movies, setToWatchMovies] = useState([]);
  const [searchResults, setSearchResults] = useState([]);
  const apiKey = "81eb1fa9c553b464701b00ef4bd78703e6b90c2829f0408b27b28f990536e663";

  useEffect(() => {
    const fetchToWatchMovies = async () => {
      try {
        const response = await fetch(
          "https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/towatchlist/entries",
          {
            headers: {
              "X-API-KEY": apiKey,
            },
          }
        );

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const jsonResponse = await response.json();
        setToWatchMovies(jsonResponse);
      } catch (error) {
        console.error("Error fetching movies:", error);
      }
    };

    fetchToWatchMovies();
  }, [apiKey]);

  const displayedMovies = searchResults.length > 0 ? searchResults : movies;

  return (
    <>
      <header>
        <NavBar onSearchComplete={setSearchResults} searchEndpoint="https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/towatchlist/entries" />
      </header>
      <main id="towatchlist">
        <div id="towatchlist-container">
          {displayedMovies.map((movie) => (
            <div className="movie" key={movie.id}>
              <div className="poster-container">
                <img src={movie.poster} alt={movie.title} />
                <div className="movie-button">
                  <AddtoList movieID={movie.movieID} />
                </div>
              </div>
              <div className="text-container">
                <h2>{movie.priority}. {movie.title}</h2>
                <div className="row">
                  <p>{movie.release_date}</p>
                  <p>{movie.runtime}m</p>
                  <p>{movie.original_language}</p>
                </div>
                <div className="row">
                  <p>⭐ {movie.vote_average} ({movie.vote_count})</p>
                  <Rating movieID={movie.movieID} />
                </div>
                <p>{movie.overview}</p>
                <div className="row">
                  <a href={movie.homepage} target="_blank" rel="noopener noreferrer">Homepage</a>
                </div>
                <UpdateMoviePriority movieID={movie.movieID} />
                <AddtoCompleteList movieID={movie.movieID} />
                
              </div>
            </div>
          ))}
        </div>
      </main>
    </>
  );
}
