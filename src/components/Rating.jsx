import { useState, useEffect } from "react";
import "../styles/Rating.css"; // Make sure to create and import your CSS file

const apiKey = "81eb1fa9c553b464701b00ef4bd78703e6b90c2829f0408b27b28f990536e663";

export default function Rating({ movieID }) {
    const [newRating, setNewRating] = useState('');
    const [currentRating, setCurrentRating] = useState(null);
    const [showRatingDiv, setShowRatingDiv] = useState(false);

    useEffect(() => {
        const fetchRating = async () => {
            try {
                const response = await fetch(
                    `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries/${movieID}/rating`,
                    {
                        method: 'GET',
                        headers: {
                            "X-API-KEY": apiKey,
                        },
                    }
                );

                if (response.ok) {
                    const result = await response.json();
                    setCurrentRating(result.rating || null); // Handle the case where rating is not found
                } else {
                    setCurrentRating(null); // Set to null if there's an error
                }
            } catch (error) {
                setCurrentRating(null); // Set to null if there's an error
            }
        };

        fetchRating();
    }, [movieID]);

    const handleRateClick = () => {
        setShowRatingDiv(!showRatingDiv);
    };

    const handleChange = (ev) => {
        setNewRating(ev.target.value);
    };

    const handleSubmit = async (ev) => {
        ev.preventDefault(); // Prevent form from refreshing the page

        try {
            const response = await fetch(
                `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries/${movieID}/rating?rating=${newRating}`,
                {
                    headers: {
                        "X-API-KEY": apiKey,
                        "Content-Type": "application/json",
                    },
                    method: 'PATCH',
                }
            );

            if (response.ok) {
                setCurrentRating(newRating);
                setShowRatingDiv(false);
            } else {
                console.error("Error submitting rating");
            }
        } catch (error) {
            console.error("Error submitting rating:", error);
        }
    };

    return (
        <div className="rating-container">
            <button type="button" onClick={handleRateClick}>
                <i className={`fa-${currentRating !== null ? 'solid' : 'regular'} fa-star ${showRatingDiv ? 'active' : ''}`}></i>
                <p>{currentRating !== null ? `${currentRating}` : 'Rate'}</p>
            </button>
            {showRatingDiv && (
                <div className="rating-div">
                    <button type="button" className="close-btn" id="close-towatchlist-btn" onClick={() => setShowRatingDiv(false)}>
                        &times;
                    </button>
                    <i className="fa-solid fa-star" id="star-div"></i>
                    <h3>RATE THIS</h3>
                    <form onSubmit={handleSubmit}>
                        <input
                            type="number"
                            min="1"
                            max="10"
                            step="0.1"
                            placeholder="Rate from 1 to 10"
                            value={newRating}
                            onChange={handleChange}
                            required
                        />
                        <button type="submit">Rate</button>
                    </form>
                </div>
            )}
        </div>
    );
}
