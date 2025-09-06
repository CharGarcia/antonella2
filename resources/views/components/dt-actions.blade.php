<div class="btn-group" role="group">
  <a href="{{ url()->current() }}/{{ $id }}" class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i>
  </a>
  <a href="{{ url()->current() }}/{{ $id }}" class="btn btn-sm btn-warning" onclick="event.preventDefault(); document.getElementById('edit-{{ $id }}').submit();">
    <i class="fas fa-edit"></i>
  </a>
  <form id="edit-{{ $id }}" method="GET" action="{{ url()->current() }}/{{ $id }}">
    @csrf
  </form>
  <form method="POST" action="{{ url()->current() }}/{{ $id }}" onsubmit="return confirm('¿Eliminar registro?');">
    @csrf @method('DELETE')
    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
  </form>
</div>
