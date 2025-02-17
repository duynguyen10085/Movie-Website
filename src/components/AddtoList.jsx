import { useState, useEffect } from "react";
import "../styles/Movie.css"; // Import your CSS file for styling

export default function AddtoList({ movieID }) {
    const [isAdded, setIsAdded] = useState(false);
    const apiKey = "81eb1fa9c553b464701b00ef4bd78703e6b90c2829f0408b27b28f990536e663";

    useEffect(() => {
        const checkMovieStatus = async () => {
            try {
                const response = await fetch(
                    `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/towatchlist/entries?movieID=${movieID}`,
                    {
                        method: 'GET',
                        headers: {
                            "X-API-KEY": apiKey,
                        },
                    }
                );

                if (response.ok) {
                    const result = await response.json();
                    if (result.status === "exists") {
                        setIsAdded(true);
                    }
                } else {
                    console.error("Error checking movie status");
                }
            } catch (error) {
                console.error("Error checking movie status:", error);
            }
        };

        checkMovieStatus();
    }, [movieID]);

    const handleClick = async () => {
        try {
            const method = isAdded ? 'DELETE' : 'POST';
            const url = isAdded
                ? `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/towatchlist/entries/${movieID}`
                : `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/towatchlist/entries?movieID=${movieID}`;

            const response = await fetch(url, {
                method: method,
                headers: {
                    "X-API-KEY": apiKey,
                },
            });

            if (response.ok) {
                setIsAdded(!isAdded); // Toggle the state
            } else {
                const error = await response.json();
                console.error(`Error ${method === 'POST' ? 'adding' : 'removing'} movie:`, error);
            }
        } catch (error) {
            console.error(`Error ${isAdded ? 'removing' : 'adding'} movie:`, error);
        }
    };

    return (
        <div className="movie-button">
            <button
                type="button"
                className={isAdded ? 'added' : ''}
                onClick={handleClick}
            />
        </div>
    );
}
