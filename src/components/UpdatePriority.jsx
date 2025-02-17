import React, { useState } from 'react';

export default function UpdateMoviePriority({ movieID }) {
  const [newPriority, setNewPriority] = useState('');
  const apiKey = "81eb1fa9c553b464701b00ef4bd78703e6b90c2829f0408b27b28f990536e663";

  const handleChange = (ev) => {
    setNewPriority(ev.target.value);
  };

  const handleSubmit = async (ev) => {
    ev.preventDefault(); // Prevent form from refreshing the page

    try {
      const response = await fetch(
        `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/towatchlist/entries/${movieID}/priority?priority=${newPriority}`, 
        {
          headers: {
            "X-API-KEY": apiKey,
            "Content-Type": "application/json",
          },
          method: 'PATCH',
        }
      );

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const result = await response.json();
      console.log('Priority update response:', result);

      // Optionally reset the priority input
      setNewPriority('');
      
    } catch (error) {
      console.error("Error updating priority:", error);
    }
  };

  return (
    <form onSubmit={handleSubmit} id="priority-form">
      <label htmlFor="newPriority">New Priority (Number):</label>
      <input
        type="number"
        id="newPriority"
        value={newPriority}
        onChange={handleChange}
        required
      />
      <button type="submit">Update Priority</button>
    </form>
  );
}
