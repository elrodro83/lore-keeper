package ar.com.lorekeeper.server.bean;

import java.util.Date;

public class Event extends PersistentObject {

	private String what;
	
	private Place where;
	
	private Date whenFrom;

	private Date whenTo;

	public String getWhat() {
		return what;
	}

	public void setWhat(String what) {
		this.what = what;
	}

	public PersistentObject getWhere() {
		return where;
	}

	public void setWhere(Place where) {
		this.where = where;
	}

	public Date getWhenFrom() {
		return whenFrom;
	}

	public void setWhenFrom(Date whenFrom) {
		this.whenFrom = whenFrom;
	}

	public Date getWhenTo() {
		return whenTo;
	}

	public void setWhenTo(Date whenTo) {
		this.whenTo = whenTo;
	}

	public boolean happenedIn(Place where) {
		return this.where.isInside(where);
	}
}
