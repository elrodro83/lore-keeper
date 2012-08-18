package ar.com.lorekeeper.server.bean;

import java.util.HashSet;
import java.util.Set;

public class Place extends PersistentObject {

	private String name;

	private String description;

	private final Set<Place> children = new HashSet<Place>();

	private Place parent;

	private GeographyCoordinate geographyCoordinate;

	public String getName() {
		return name;
	}

	public void setName(final String name) {
		this.name = name;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(final String description) {
		this.description = description;
	}

	public void addChild(final Place child) {
		children.add(child);
		child.setParent(this);
	}

	public Place getParent() {
		return parent;
	}

	protected void setParent(final Place parent) {
		this.parent = parent;
	}

	public boolean isInside(final Place where) {
		if (this.parent == null) {
			return false;
		} else {
			return this.parent.equals(where) || this.parent.isInside(where);
		}
	}

	public Object getId() {
		return getName();
	}

	public long getVersion() {
		return 0;
	}
}
