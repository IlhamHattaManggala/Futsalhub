@extends('layouts.app')

@section('title', 'Tactical Board')
@section('header_title', 'Tactical Board Digital')

@section('styles')
<style>
    canvas {
        background-color: #1e3f20;
        border: 4px solid #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        cursor: default;
        width: 100%;
        height: auto;
        max-width: 100%;
    }
    .tool-active {
        background-color: rgba(5, 150, 105, 0.1) !important;
        border-color: #059669 !important;
        color: #059669 !important;
    }
</style>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h3 class="text-lg font-bold text-slate-900"><i class="fa-solid fa-compass-drafting text-emerald-600 mr-2"></i>Workspace Tactical Board</h3>
        <p class="text-xs text-slate-500">Atur posisi pemain, preset formasi, dan gambar pergerakan strategi Anda secara interaktif</p>
    </div>
    <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 shadow-sm shrink-0">
        <span id="saveStatus" class="text-[10px] text-slate-500 font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-circle-check text-emerald-500"></i> Tersimpan Otomatis
        </span>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
    <!-- Left Column: HTML5 Canvas -->
    <div class="xl:col-span-8 flex flex-col items-center">
        <!-- Canvas field representation -->
        <div class="w-full relative overflow-x-auto pb-4 flex justify-center">
            <canvas id="tacticalCanvas" width="800" height="500"></canvas>
        </div>
        
        <!-- Canvas Status Info -->
        <div id="canvasStatusInfo" class="w-full max-w-[800px] flex justify-between items-center px-4 py-3 bg-white rounded-2xl border border-slate-200 text-xs text-slate-500 shadow-sm">
            <span><i class="fa-solid fa-lightbulb text-yellow-500 mr-1.5"></i> <strong>Tips:</strong> Drag & Drop token pemain atau bola secara bebas. Ubah ke mode Coret/Pensil untuk menggambar coretan taktis.</span>
            <span id="canvasCoords" class="font-mono text-[10px] text-slate-400">X: 0, Y: 0</span>
        </div>
    </div>

    <!-- Right Column: Controls Dashboard -->
    <div class="xl:col-span-4 space-y-6">
        <!-- Mode Switchers & Tools -->
        <div class="card-white p-6 rounded-3xl space-y-5">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Peralatan Papan</h4>
            
            <div class="grid grid-cols-2 gap-3">
                <button type="button" id="toolDrag" class="py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-bold text-xs transition-all flex items-center justify-center gap-2 tool-active">
                    <i class="fa-solid fa-hand"></i> Seret Objek
                </button>
                <button type="button" id="toolPencil" class="py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-bold text-xs transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-pencil"></i> Coret/Pensil
                </button>
            </div>

            <!-- Sketch Colors -->
            <div id="drawingOptions" class="hidden space-y-3">
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Warna Corengan</span>
                <div class="flex gap-2">
                    <button type="button" onclick="setStrokeColor('#facc15')" class="w-8 h-8 rounded-full border border-slate-300 bg-yellow-450 flex items-center justify-center text-slate-900 shadow-sm" id="colorYellow"><i class="fa-solid fa-check text-xs"></i></button>
                    <button type="button" onclick="setStrokeColor('#ffffff')" class="w-8 h-8 rounded-full border border-slate-300 bg-white flex items-center justify-center text-slate-900 shadow-sm" id="colorWhite"></button>
                    <button type="button" onclick="setStrokeColor('#ef4444')" class="w-8 h-8 rounded-full border border-slate-300 bg-red-500 flex items-center justify-center text-white shadow-sm" id="colorRed"></button>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="button" id="btnClearDrawings" class="flex-1 py-2 bg-red-50 border border-red-200 hover:bg-red-100/50 text-red-600 text-xs font-bold rounded-xl transition-all shadow-sm">
                    <i class="fa-solid fa-eraser mr-1"></i> Hapus Corengan
                </button>
                <button type="button" id="btnResetBoard" class="flex-1 py-2 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition-all shadow-sm">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset Papan
                </button>
            </div>
        </div>


    </div>
</div>
@endsection

@section('scripts')
<script>
    // Canvas & Context Setup
    const canvas = document.getElementById('tacticalCanvas');
    const ctx = canvas.getContext('2d');
    const coordsDisplay = document.getElementById('canvasCoords');
    const canvasStatusInfo = document.getElementById('canvasStatusInfo');

    // Orientation State
    let isPortrait = window.innerWidth < 1024;
    if (isPortrait) {
        canvas.width = 500;
        canvas.height = 800;
        canvas.style.maxWidth = '500px';
        if (canvasStatusInfo) canvasStatusInfo.style.maxWidth = '500px';
    } else {
        canvas.width = 800;
        canvas.height = 500;
        canvas.style.maxWidth = '800px';
        if (canvasStatusInfo) canvasStatusInfo.style.maxWidth = '800px';
    }

    // Mathematical coordinate rotation helpers
    function toPortrait(x, y) {
        return {
            x: 500 - y,
            y: x
        };
    }

    function toLandscape(x, y) {
        return {
            x: y,
            y: 500 - x
        };
    }

    function rotateStateToPortrait() {
        players = players.map(p => {
            const rot = toPortrait(p.x, p.y);
            return { ...p, x: rot.x, y: rot.y };
        });
        opponents = opponents.map(o => {
            const rot = toPortrait(o.x, o.y);
            return { ...o, x: rot.x, y: rot.y };
        });
        const rotBall = toPortrait(ball.x, ball.y);
        ball.x = rotBall.x;
        ball.y = rotBall.y;
        
        sketches = sketches.map(stroke => ({
            ...stroke,
            points: stroke.points ? stroke.points.map(pt => toPortrait(pt.x, pt.y)) : []
        }));
    }

    function rotateStateToLandscape() {
        players = players.map(p => {
            const rot = toLandscape(p.x, p.y);
            return { ...p, x: rot.x, y: rot.y };
        });
        opponents = opponents.map(o => {
            const rot = toLandscape(o.x, o.y);
            return { ...o, x: rot.x, y: rot.y };
        });
        const rotBall = toLandscape(ball.x, ball.y);
        ball.x = rotBall.x;
        ball.y = rotBall.y;
        
        sketches = sketches.map(stroke => ({
            ...stroke,
            points: stroke.points ? stroke.points.map(pt => toLandscape(pt.x, pt.y)) : []
        }));
    }

    // Ball Image Setup
    const ballImage = new Image();
    ballImage.src = "{{ asset('images/bola.png') }}";
    ballImage.onload = function() {
        if (typeof drawBoard === 'function') {
            drawBoard();
        }
    };

    // Drawing variables
    let currentTool = 'drag'; // 'drag' or 'pencil'
    let strokeColor = '#facc15';
    let isDrawing = false;
    let activeStrokePoints = [];
    let sketches = [];

    // State Variables
    let draggedObject = null;
    let dragOffset = { x: 0, y: 0 };

    // Default Players positions (always in landscape 800x500 schema)
    let players = [];
    let opponents = [];
    let ball = { x: 400, y: 250, radius: 13, color: '#ffffff' };

    // Load saved tactic data from DB if exists
    const savedData = @json($tactic ? $tactic->canvas_data : null);
    let selectedFormation = 'Custom';

    // Initialize Default Setup
    function initTactics() {
        if (savedData) {
            // Load and fix off-screen coordinate issues (e.g. GK seeder bug where y=540 is outside 500px canvas height)
            players = savedData.players.map(p => {
                let x = p.x;
                let y = p.y;
                if (y > 500) y = 250;
                if (x > 800) x = 400;
                return { ...p, x: x, y: y, radius: 20, color: '#2563eb' };
            });
            opponents = savedData.opponents.map(o => {
                let x = o.x;
                let y = o.y;
                if (y > 500) y = 250;
                if (x > 800) x = 400;
                return { ...o, x: x, y: y, radius: 20, color: '#dc2626' };
            });
            ball = { x: savedData.ball.x, y: savedData.ball.y, radius: 13, color: '#ffffff' };
            
            // Normalize old drawings (convert from/to formats to points array)
            sketches = (savedData.drawings || []).map(stroke => {
                if (!stroke.points && stroke.from && stroke.to) {
                    return {
                        points: [stroke.from, stroke.to],
                        color: stroke.color || '#ffffff'
                    };
                }
                return stroke;
            }).filter(stroke => stroke.points && stroke.points.length >= 2);
        } else {
            // Our team (Blue tokens) - Defends LEFT, Attacks RIGHT
            players = [
                { id: 'p1', role: 'our', number: 1, name: 'GK', x: 80, y: 250, radius: 20, color: '#2563eb' },
                { id: 'p2', role: 'our', number: 4, name: 'ANC', x: 220, y: 250, radius: 20, color: '#2563eb' },
                { id: 'p3', role: 'our', number: 7, name: 'FL1', x: 380, y: 110, radius: 20, color: '#2563eb' },
                { id: 'p4', role: 'our', number: 11, name: 'FL2', x: 380, y: 390, radius: 20, color: '#2563eb' },
                { id: 'p5', role: 'our', number: 9, name: 'PIV', x: 540, y: 250, radius: 20, color: '#2563eb' },
            ];

            // Enemy team (Red tokens) - Defends RIGHT, Attacks LEFT
            opponents = [
                { id: 'o1', role: 'enemy', number: 99, name: 'GK', x: 720, y: 250, radius: 20, color: '#dc2626' },
                { id: 'o2', role: 'enemy', number: 5, name: 'DEF', x: 580, y: 250, radius: 20, color: '#dc2626' },
                { id: 'o3', role: 'enemy', number: 10, name: 'MID1', x: 420, y: 130, radius: 20, color: '#dc2626' },
                { id: 'o4', role: 'enemy', number: 8, name: 'MID2', x: 420, y: 370, radius: 20, color: '#dc2626' },
                { id: 'o5', role: 'enemy', number: 7, name: 'FWD', x: 260, y: 250, radius: 20, color: '#dc2626' },
            ];

            ball = { x: 400, y: 250, radius: 13, color: '#ffffff' };
            sketches = [];
        }

        // If page is loaded in portrait viewport, rotate all standard landscape coordinates
        if (isPortrait) {
            rotateStateToPortrait();
        }

        drawBoard();
    }

    // DRAWING THE ENTIRE BOARD (Dynamic Landscape/Portrait Pitch)
    function drawBoard() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // 1. Draw Field Background
        ctx.fillStyle = '#1e3f20';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // 2. Draw Lines
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.6)';
        ctx.lineWidth = 3.5;

        // Outer Bound Line
        const marginX = 35;
        const marginY = 35;
        ctx.strokeRect(marginX, marginY, canvas.width - (marginX * 2), canvas.height - (marginY * 2));

        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;

        if (isPortrait) {
            // Center line (Horizontal in Portrait)
            ctx.beginPath();
            ctx.moveTo(marginX, centerY);
            ctx.lineTo(canvas.width - marginX, centerY);
            ctx.stroke();

            // Center Circle
            ctx.beginPath();
            ctx.arc(centerX, centerY, 65, 0, 2 * Math.PI);
            ctx.stroke();

            // Center Spot
            ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
            ctx.beginPath();
            ctx.arc(centerX, centerY, 5, 0, 2 * Math.PI);
            ctx.fill();

            // Penalty Areas (Top / Bottom)
            // Top Penalty Area
            ctx.beginPath();
            ctx.arc(centerX, marginY, 90, 0, Math.PI);
            ctx.stroke();

            // Bottom Penalty Area
            ctx.beginPath();
            ctx.arc(centerX, canvas.height - marginY, 90, Math.PI, 2 * Math.PI);
            ctx.stroke();

            // Penalty Spots (6m)
            ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
            ctx.beginPath();
            ctx.arc(centerX, marginY + 60, 3.5, 0, 2 * Math.PI); // Top
            ctx.arc(centerX, canvas.height - marginY - 60, 3.5, 0, 2 * Math.PI); // Bottom
            ctx.fill();

            // Goalposts & nets outside the boundaries
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 3;
            // Top goal net
            ctx.strokeRect(centerX - 45, marginY - 15, 90, 15);
            // Bottom goal net
            ctx.strokeRect(centerX - 45, canvas.height - marginY, 90, 15);
        } else {
            // Center line (Vertical in Landscape)
            ctx.beginPath();
            ctx.moveTo(centerX, marginY);
            ctx.lineTo(centerX, canvas.height - marginY);
            ctx.stroke();

            // Center Circle
            ctx.beginPath();
            ctx.arc(centerX, centerY, 65, 0, 2 * Math.PI);
            ctx.stroke();

            // Center Spot
            ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
            ctx.beginPath();
            ctx.arc(centerX, centerY, 5, 0, 2 * Math.PI);
            ctx.fill();

            // Penalty Areas (Left / Right)
            // Left Penalty Area (Home)
            ctx.beginPath();
            ctx.arc(marginX, centerY, 90, -Math.PI / 2, Math.PI / 2);
            ctx.stroke();

            // Right Penalty Area (Away)
            ctx.beginPath();
            ctx.arc(canvas.width - marginX, centerY, 90, Math.PI / 2, 3 * Math.PI / 2);
            ctx.stroke();

            // Penalty Spots (6m)
            ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
            ctx.beginPath();
            ctx.arc(marginX + 60, centerY, 3.5, 0, 2 * Math.PI); // Left
            ctx.arc(canvas.width - marginX - 60, centerY, 3.5, 0, 2 * Math.PI); // Right
            ctx.fill();

            // Goalposts & nets outside the boundaries
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 3;
            // Left goal net
            ctx.strokeRect(marginX - 15, centerY - 45, 15, 90);
            // Right goal net
            ctx.strokeRect(canvas.width - marginX, centerY - 45, 15, 90);
        }

        // 3. Draw Sketches
        for (let stroke of sketches) {
            drawStroke(stroke);
        }

        // Draw Active Stroke in progress
        if (isDrawing && activeStrokePoints.length > 1) {
            drawStroke({ points: activeStrokePoints, color: strokeColor });
        }

        // 4. Draw Opponents (Red Team)
        for (let enemy of opponents) {
            drawToken(enemy);
        }

        // 5. Draw Our Players (Blue Team)
        for (let player of players) {
            drawToken(player);
        }

        // 6. Draw Ball
        drawBallToken(ball);
    }

    // Draw round player token
    function drawToken(token) {
        ctx.shadowBlur = 6;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.2)';
        
        ctx.fillStyle = token.color;
        ctx.beginPath();
        ctx.arc(token.x, token.y, token.radius, 0, 2 * Math.PI);
        ctx.fill();

        // Reset shadow
        ctx.shadowBlur = 0;

        ctx.strokeStyle = token.role === 'our' ? '#facc15' : '#ffffff';
        ctx.lineWidth = 2.5;
        ctx.stroke();

        // Number Text
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 13px Outfit';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(token.number, token.x, token.y);

        // Name Tag under player
        ctx.fillStyle = '#e2e8f0';
        ctx.font = 'bold 10px Outfit';
        ctx.fillText(token.name, token.x, token.y + token.radius + 12);
    }

    // Draw ball token (loaded image or realistic vector soccer ball fallback)
    function drawBallToken(ballObj) {
        const x = ballObj.x;
        const y = ballObj.y;
        const r = ballObj.radius || 13;

        ctx.save();
        
        // Shadow for the ball to pop out
        ctx.shadowBlur = 6;
        ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
        ctx.shadowOffsetX = 1.5;
        ctx.shadowOffsetY = 1.5;

        if (ballImage.complete && ballImage.naturalWidth !== 0) {
            ctx.drawImage(ballImage, x - r, y - r, r * 2, r * 2);
        } else {
            // White base circle fallback
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(x, y, r, 0, 2 * Math.PI);
            ctx.fill();

            // Stroke outer border
            ctx.strokeStyle = '#1e293b';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Draw central black pentagon
            ctx.fillStyle = '#1e293b';
            ctx.beginPath();
            for (let i = 0; i < 5; i++) {
                const angle = (i * 2 * Math.PI / 5) - Math.PI / 2;
                const px = x + (r * 0.35) * Math.cos(angle);
                const py = y + (r * 0.35) * Math.sin(angle);
                if (i === 0) ctx.moveTo(px, py);
                else ctx.lineTo(px, py);
            }
            ctx.closePath();
            ctx.fill();

            // Draw 5 radial lines and edge panels to complete the soccer pattern
            ctx.strokeStyle = '#1e293b';
            ctx.lineWidth = 1.2;
            for (let i = 0; i < 5; i++) {
                const angle = (i * 2 * Math.PI / 5) - Math.PI / 2;
                const startX = x + (r * 0.35) * Math.cos(angle);
                const startY = y + (r * 0.35) * Math.sin(angle);
                const endX = x + r * Math.cos(angle);
                const endY = y + r * Math.sin(angle);
                
                ctx.beginPath();
                ctx.moveTo(startX, startY);
                ctx.lineTo(endX, endY);
                ctx.stroke();

                // Draw outer black panels
                ctx.fillStyle = '#1e293b';
                ctx.beginPath();
                const nextAngle = ((i + 1) * 2 * Math.PI / 5) - Math.PI / 2;
                const midAngle = (angle + nextAngle) / 2;
                ctx.moveTo(x + r * Math.cos(midAngle - 0.25), y + r * Math.sin(midAngle - 0.25));
                ctx.lineTo(x + r * Math.cos(midAngle + 0.25), y + r * Math.sin(midAngle + 0.25));
                ctx.lineTo(x + (r * 0.7) * Math.cos(midAngle), y + (r * 0.7) * Math.sin(midAngle));
                ctx.closePath();
                ctx.fill();
            }
        }
        ctx.restore();
    }

    // Draw stroke (line + arrowhead)
    function drawStroke(stroke) {
        if (!stroke.points || stroke.points.length < 2) return;

        ctx.strokeStyle = stroke.color;
        ctx.lineWidth = 4;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        ctx.beginPath();
        ctx.moveTo(stroke.points[0].x, stroke.points[0].y);
        for (let i = 1; i < stroke.points.length; i++) {
            ctx.lineTo(stroke.points[i].x, stroke.points[i].y);
        }
        ctx.stroke();

        const p1 = stroke.points[stroke.points.length - 2];
        const p2 = stroke.points[stroke.points.length - 1];
        
        const angle = Math.atan2(p2.y - p1.y, p2.x - p1.x);
        ctx.fillStyle = stroke.color;
        ctx.beginPath();
        ctx.moveTo(p2.x, p2.y);
        ctx.lineTo(p2.x - 12 * Math.cos(angle - Math.PI / 6), p2.y - 12 * Math.sin(angle - Math.PI / 6));
        ctx.lineTo(p2.x - 12 * Math.cos(angle + Math.PI / 6), p2.y - 12 * Math.sin(angle + Math.PI / 6));
        ctx.closePath();
        ctx.fill();
    }

    function getMousePos(e) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: (e.clientX - rect.left) * (canvas.width / rect.width),
            y: (e.clientY - rect.top) * (canvas.height / rect.height)
        };
    }

    function checkHit(pos, obj) {
        const dx = pos.x - obj.x;
        const dy = pos.y - obj.y;
        return Math.sqrt(dx * dx + dy * dy) < obj.radius;
    }

    canvas.addEventListener('mousemove', function (e) {
        const pos = getMousePos(e);
        coordsDisplay.innerText = `X: ${Math.round(pos.x)}, Y: ${Math.round(pos.y)}`;

        if (currentTool === 'drag' && draggedObject) {
            draggedObject.x = pos.x + dragOffset.x;
            draggedObject.y = pos.y + dragOffset.y;
            drawBoard();
        } else if (currentTool === 'pencil' && isDrawing) {
            activeStrokePoints.push(pos);
            drawBoard();
        }
    });

    canvas.addEventListener('mousedown', function (e) {
        const pos = getMousePos(e);

        if (currentTool === 'drag') {
            if (checkHit(pos, ball)) {
                draggedObject = ball;
                dragOffset.x = ball.x - pos.x;
                dragOffset.y = ball.y - pos.y;
                return;
            }

            for (let player of players) {
                if (checkHit(pos, player)) {
                    draggedObject = player;
                    dragOffset.x = player.x - pos.x;
                    dragOffset.y = player.y - pos.y;
                    return;
                }
            }

            for (let enemy of opponents) {
                if (checkHit(pos, enemy)) {
                    draggedObject = enemy;
                    dragOffset.x = enemy.x - pos.x;
                    dragOffset.y = enemy.y - pos.y;
                    return;
                }
            }
        } else if (currentTool === 'pencil') {
            isDrawing = true;
            activeStrokePoints = [pos];
        }
    });

    window.addEventListener('mouseup', function () {
        if (currentTool === 'drag') {
            if (draggedObject) {
                draggedObject = null;
                autoSaveTactic();
            }
        } else if (currentTool === 'pencil' && isDrawing) {
            isDrawing = false;
            if (activeStrokePoints.length > 1) {
                sketches.push({
                    points: activeStrokePoints,
                    color: strokeColor
                });
                autoSaveTactic();
            }
            activeStrokePoints = [];
            drawBoard();
        }
    });

    // Touch support
    canvas.addEventListener('touchmove', function (e) {
        if (e.touches.length === 1) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }
    }, { passive: false });

    canvas.addEventListener('touchstart', function (e) {
        if (e.touches.length === 1) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }
    }, { passive: false });

    canvas.addEventListener('touchend', function (e) {
        const mouseEvent = new MouseEvent('mouseup', {});
        window.dispatchEvent(mouseEvent);
    });

    // Tool Toggling
    const btnDrag = document.getElementById('toolDrag');
    const btnPencil = document.getElementById('toolPencil');
    const drawOptions = document.getElementById('drawingOptions');

    btnDrag.addEventListener('click', () => {
        currentTool = 'drag';
        btnDrag.classList.add('tool-active');
        btnPencil.classList.remove('tool-active');
        drawOptions.classList.add('hidden');
        canvas.style.cursor = 'default';
    });

    btnPencil.addEventListener('click', () => {
        currentTool = 'pencil';
        btnPencil.classList.add('tool-active');
        btnDrag.classList.remove('tool-active');
        drawOptions.classList.remove('hidden');
        canvas.style.cursor = 'crosshair';
    });

    // Stroke Color buttons
    const colorsBtn = {
        '#facc15': document.getElementById('colorYellow'),
        '#ffffff': document.getElementById('colorWhite'),
        '#ef4444': document.getElementById('colorRed')
    };

    function setStrokeColor(color) {
        strokeColor = color;
        for (let c in colorsBtn) {
            if (colorsBtn[c]) {
                if (c === color) {
                    colorsBtn[c].innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
                } else {
                    colorsBtn[c].innerHTML = '';
                }
            }
        }
    }

    document.getElementById('btnClearDrawings').addEventListener('click', () => {
        sketches = [];
        drawBoard();
        autoSaveTactic();
    });

    document.getElementById('btnResetBoard').addEventListener('click', () => {
        // Reset to original positions (landscape values)
        players = [
            { id: 'p1', role: 'our', number: 1, name: 'GK', x: 80, y: 250, radius: 20, color: '#2563eb' },
            { id: 'p2', role: 'our', number: 4, name: 'ANC', x: 220, y: 250, radius: 20, color: '#2563eb' },
            { id: 'p3', role: 'our', number: 7, name: 'FL1', x: 380, y: 110, radius: 20, color: '#2563eb' },
            { id: 'p4', role: 'our', number: 11, name: 'FL2', x: 380, y: 390, radius: 20, color: '#2563eb' },
            { id: 'p5', role: 'our', number: 9, name: 'PIV', x: 540, y: 250, radius: 20, color: '#2563eb' },
        ];
        opponents = [
            { id: 'o1', role: 'enemy', number: 99, name: 'GK', x: 720, y: 250, radius: 20, color: '#dc2626' },
            { id: 'o2', role: 'enemy', number: 5, name: 'DEF', x: 580, y: 250, radius: 20, color: '#dc2626' },
            { id: 'o3', role: 'enemy', number: 10, name: 'MID1', x: 420, y: 130, radius: 20, color: '#dc2626' },
            { id: 'o4', role: 'enemy', number: 8, name: 'MID2', x: 420, y: 370, radius: 20, color: '#dc2626' },
            { id: 'o5', role: 'enemy', number: 7, name: 'FWD', x: 260, y: 250, radius: 20, color: '#dc2626' },
        ];
        ball = { x: 400, y: 250, radius: 13, color: '#ffffff' };
        sketches = [];

        // If currently in portrait mode, rotate the coordinates accordingly
        if (isPortrait) {
            rotateStateToPortrait();
        }

        drawBoard();
        autoSaveTactic();
    });

    // Auto-Save AJAX
    function autoSaveTactic() {
        const saveStatus = document.getElementById('saveStatus');
        if (saveStatus) {
            saveStatus.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-blue-500 mr-1.5"></i> Menyimpan...';
        }

        let savedPlayers, savedOpponents, savedBall, savedDrawings;

        // DB always expects landscape coords (800x500)
        if (isPortrait) {
            savedPlayers = players.map(p => {
                const rot = toLandscape(p.x, p.y);
                return { id: p.id, role: p.role, number: p.number, name: p.name, x: rot.x, y: rot.y };
            });
            savedOpponents = opponents.map(o => {
                const rot = toLandscape(o.x, o.y);
                return { id: o.id, role: o.role, number: o.number, name: o.name, x: rot.x, y: rot.y };
            });
            const rotBall = toLandscape(ball.x, ball.y);
            savedBall = { x: rotBall.x, y: rotBall.y };
            savedDrawings = sketches.map(stroke => ({
                ...stroke,
                points: stroke.points ? stroke.points.map(pt => toLandscape(pt.x, pt.y)) : []
            }));
        } else {
            savedPlayers = players.map(p => ({ id: p.id, role: p.role, number: p.number, name: p.name, x: p.x, y: p.y }));
            savedOpponents = opponents.map(o => ({ id: o.id, role: o.role, number: o.number, name: o.name, x: o.x, y: o.y }));
            savedBall = { x: ball.x, y: ball.y };
            savedDrawings = sketches;
        }

        const canvasData = {
            players: savedPlayers,
            opponents: savedOpponents,
            ball: savedBall,
            drawings: savedDrawings
        };

        fetch("{{ route('tactics.save') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                title: 'Taktik Tim',
                description: 'Papan Taktik Otomatis',
                formation: selectedFormation,
                canvas_data: JSON.stringify(canvasData)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (saveStatus) {
                    saveStatus.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500"></i> Tersimpan Otomatis';
                }
            } else {
                if (saveStatus) {
                    saveStatus.innerHTML = '<i class="fa-solid fa-circle-xmark text-red-500"></i> Gagal Menyimpan';
                }
            }
        })
        .catch(err => {
            console.error(err);
            if (saveStatus) {
                saveStatus.innerHTML = '<i class="fa-solid fa-circle-xmark text-red-500"></i> Gagal (Offline)';
            }
        });
    }

    // Dynamic Orientation Change Resize Listener
    window.addEventListener('resize', () => {
        const newIsPortrait = window.innerWidth < 1024;
        if (newIsPortrait !== isPortrait) {
            isPortrait = newIsPortrait;
            if (isPortrait) {
                canvas.width = 500;
                canvas.height = 800;
                canvas.style.maxWidth = '500px';
                if (canvasStatusInfo) canvasStatusInfo.style.maxWidth = '500px';
                rotateStateToPortrait();
            } else {
                canvas.width = 800;
                canvas.height = 500;
                canvas.style.maxWidth = '800px';
                if (canvasStatusInfo) canvasStatusInfo.style.maxWidth = '800px';
                rotateStateToLandscape();
            }
            drawBoard();
        }
    });

    initTactics();
</script>
@endsection
