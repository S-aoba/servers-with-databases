<div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Random Computer</h5>
    <p class="card-text">
      <strong>CPU :</strong><br />
        <span>Name: </span><?= htmlspecialchars($computer['cpu']['name']) ?><br />
      <strong>GPU :</strong><br />
        <span>Name: </span><?= htmlspecialchars($computer['gpu']['name']) ?><br />
      <strong>Motherboard :</strong><br />
        <span>Name: </span><?= htmlspecialchars($computer['motherboard']['name']) ?><br />
      <strong>Power :</strong><br />
        <span>Name: </span><?= htmlspecialchars($computer['power']['name']) ?><br />
      <strong>Memory :</strong><br />
        <span>Name: </span><?= htmlspecialchars($computer['memory']['name']) ?><br />
      <strong>SSD :</strong><br />
      <span>Name: </span><?= htmlspecialchars($computer['ssd']['name']) ?><br />
  </div>
</div>
