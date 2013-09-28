package ar.com.lorekeeper.server.bean;

public class SearchResult {

	private final String type;
	private final Long id;
	private final String name;
	private final String description;

	public SearchResult(final String type, final Long id, final String name,
			final String description) {
		this.type = type;
		this.id = id;
		this.name = name;
		this.description = description;
	}

	public String getType() {
		return type;
	}

	public Long getId() {
		return id;
	}

	public String getName() {
		return name;
	}

	public String getDescription() {
		return description;
	}
}
