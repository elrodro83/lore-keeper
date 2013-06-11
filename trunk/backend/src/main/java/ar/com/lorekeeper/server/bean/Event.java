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

	public void setWhat(final String what) {
		this.what = what;
	}

	public PersistentObject getWhere() {
		return where;
	}

	public void setWhere(final Place where) {
		this.where = where;
	}

	public Date getWhenFrom() {
		return whenFrom;
	}

	public void setWhenFrom(final Date whenFrom) {
		this.whenFrom = whenFrom;
	}

	public Date getWhenTo() {
		return whenTo;
	}

	public void setWhenTo(final Date whenTo) {
		this.whenTo = whenTo;
	}

	public boolean happenedIn(final Place where) {
		return this.where.isInside(where);
	}

	@Override
	public Object getId() {
		return getWhat();
	}
}
