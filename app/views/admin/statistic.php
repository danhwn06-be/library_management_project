<?php $pageCSS = 'statistic.css'; ?>
<?php require_once __DIR__ . '/../layouts/headerAdmin.php'; ?>

    <h2 class="title">GENERAL STATISTICS</h2>
    <div class="stats-container">
    <div class="stats-row">
        <div class="stats-left">
            <div class="stat-box box-red">
                <div class="stat-number"><?php echo $generalStats['total_books']; ?></div>
                <div class="stat-label">Total Books</div>
            </div>
            
            <div class="stat-box box-blue-light">
                <div class="stat-number"><?php echo $generalStats['total_requests']; ?></div>
                <div class="stat-label">Borrowing request</div>
            </div>

            <div class="stat-box" style="background-color: #ffebee; border-left: 4px solid #ef5350; cursor: pointer;" onclick="window.location='index.php?action=admin_overdue_list'">
                <div class="stat-number" style="color: #c62828;"><?php echo $overdueCount ?? 0; ?></div>
                <div class="stat-label" style="color: #c62828;">Overdue requests <i class="bi bi-arrow-right"></i></div>
            </div>
        </div>

        <div class="stats-right">
            <h4 class="main-title">BOOKS BY CATEGORY STATISTICS</h4>
            
            <div class="category-grid">
                <?php 
                $colors = ['#D1C4E9', '#C8E6C9', '#FFF9C4', '#F8BBD0', '#FFCCBC', '#B3E5FC'];
                foreach ($categoryStats as $index => $stat): 
                    $color = $colors[$index % count($colors)];
                ?>
                    <div class="category-item" style="background-color: <?php echo $color; ?>;">
                        <div class="cat-number"><?php echo $stat['count']; ?></div>
                        <div class="cat-name"><?php echo $stat['categories_name']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="regulations-section">
        <div class="regulations-content">
            <h5><span class="icon">💡</span> REGULATIONS :</h5>
            <ul>
                <li>Borrowing period: 14 days</li>
            </ul>
        </div>
    </div>
</div>

</body>
</html>
