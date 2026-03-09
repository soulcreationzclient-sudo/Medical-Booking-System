import React from "react";
import { createRoot } from "react-dom/client";

function Loginform() {
    return (
        <>
            <h1>React with Laravel</h1>
            <input type="text" />
        </>
    );
}

const container = document.getElementById("app");

if (container) {
    const root = createRoot(container);
    root.render(<Loginform />);
}
