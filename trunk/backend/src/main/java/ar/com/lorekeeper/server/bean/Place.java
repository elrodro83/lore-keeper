package ar.com.lorekeeper.server.bean;

import java.util.HashSet;
import java.util.Set;

public class Place extends PersistentObject {
	
	private String name;
	
	private String description;
	
	private Set<Place> children = new HashSet<Place>();
	
	private Place parent;
	
	private GeographyCoordinate geographyCoordinate;

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public void addChild(Place child) {
		children.add(child);
		child.setParent(this);
	}

	public Place getParent() {
		return parent;
	}

	protected void setParent(Place parent) {
		this.parent = parent;
	}

	public boolean isInside(Place where) {
		if(this.parent == null) {
			return false;
		} else {
			return this.parent.equals(where) || this.parent.isInside(where);
		}
	}
	
}
