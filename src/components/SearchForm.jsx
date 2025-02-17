import { useState } from "react";

const apiKey = "81eb1fa9c553b464701b00ef4bd78703e6b90c2829f0408b27b28f990536e663";
export default function SearchForm({ onSearchComplete, searchEndpoint }) {
  const [title, setTitle] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const updateSearchTitle = (ev) => {
    setTitle(ev.target.value);
  };

  const handleSubmit = async (ev) => {
    ev.preventDefault();
    setLoading(true);
    setError("");
    try {
      const response = await fetch(`${searchEndpoint}?title=${title}`, 
      {
        headers: {
          "X-API-KEY": apiKey,
          "Content-Type": "application/json",
      },
        method: 'GET',
      }
    );
      
      if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`);
      }
      const jsonResponse = await response.json();
      onSearchComplete(jsonResponse);
    } catch (error) {
      setError(error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} id="search-form">
      <input
        type="text"
        value={title}
        onChange={updateSearchTitle}
        placeholder="Search for a movie title..."
      />
      <button type="submit">Search</button>
      {loading && <p>Loading...</p>}
      {error && <p style={{ color: "red" }}>{error}</p>}
    </form>
  );
}
