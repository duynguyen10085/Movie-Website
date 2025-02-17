import React, { useState, useEffect } from "react";
import NavBar from "../NavBar";
import "../../styles/CompletedWatchedList.css";
import Rating from "../Rating";
import AddtoCompleteList from "../AddtoCompleteList";
import SearchForm from "../SearchForm"; // Import SearchForm component

export default function CompletedWatchList() {
  const [movies, setWatchedMovies] = useState([]);
  const [searchResults, setSearchResults] = useState([]);
  const apiKey = "81eb1fa9c553b464701b00ef4bd78703e6b90c2829f0408b27b28f990536e663";

  useEffect(() => {
    const fetchWatchedMovies = async () => {
      try {
        const response = await fetch(
          `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries`,
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
        setWatchedMovies(jsonResponse);
        setSearchResults(jsonResponse); // Initialize search results with all movies
      } catch (error) {
        console.error("Error fetching movies:", error);
      }
    };

    fetchWatchedMovies();
  }, [apiKey]);

  // This function will handle the search results from the SearchForm component
  const handleSearchComplete = (results) => {
    setSearchResults(results);
  };

  const WatchedTime = ({ movieID }) => {
    const [count, setCount] = useState(0);

    useEffect(() => {
      const fetchWatchCount = async () => {
        try {
          const response = await fetch(
            `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries/${movieID}/times-watched`,
            {
              headers: {
                "X-API-KEY": apiKey,
              }
            }
          );

          if (response.ok) {
            const result = await response.json();
            setCount(result.timesWatched || 0);
          } else {
            console.error("Error fetching watch count:", response.statusText);
          }
        } catch (error) {
          console.error("Error fetching watch count:", error);
        }
      };

      fetchWatchCount();
    }, [apiKey, movieID]);

    return <p>Times Watched: {count}</p>;
  };

  const displayedMovies = searchResults.length > 0 ? searchResults : movies;

  return (
    <>
      <header>
        <NavBar onSearchComplete={handleSearchComplete} searchEndpoint="https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries" />
      </header>
      <main id="completedWatchList">
        <div id="completedWatchList-container">
          {displayedMovies.length > 0 ? (
            displayedMovies.map((movie) => (
              <div className="movie" key={movie.movieID}>
                <div className="poster-container">
                  <img src={movie.poster} alt={movie.title} />
                </div>
                <div className="text-container">
                  <h2>{movie.title}</h2>
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
                    <WatchedTime movieID={movie.movieID} />
                  </div>
                  <AddtoCompleteList movieID={movie.movieID} />
                </div>
              </div>
            ))
          ) : (
            <p>No movies found.</p>
          )}
        </div>
      </main>
    </>
  );
}
