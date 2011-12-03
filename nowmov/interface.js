var Interface = function(channel, base_url){
	var self = this;
	this.channel = channel;
	Channels.channel_name = channel.channel_name;
	this.video_ctr = 1;
	this.video_index = 0;
	this.count_watched = 0;
	this.twitter_api_ctr = 0;
	this.twitter_api_limit = 130;
	this.is_shared_link = false;	
	this.base_url = base_url;
	this.title = document.title;
	this.rendering = false;
	
	var FBD_enabled = true;
	var twttr_enabled = true;

	this.Utility = new Utility();
	this.Address = new Address();
	this.Notify = new Notify();
	this.Seeker = new Seeker();
	this.Ranger = new Ranger();
	this.Volume = new Volume();
	this.Quality = new Quality();
	this.Fullscreen = new Fullscreen();
	this.Shortcuts = new Shortcuts();
	this.Hints = new Hints();
	this.Bindings = new Bindings();
	this.Mentions = new Mentions();
	this.Canvas = new Canvas();
	this.Embed = new Embed();
	this.Landing = new Landing();
	this.Upcoming = new Upcoming();
	this.Queue = new Queue();
	this.Context = new Context();
	//this.Explorer = new Explorer();
		
	// Init
	this.Volume.setLevels();
	this.Shortcuts.add();
	this.Queue.init();
	Share.init();
};

/* Followed by ~2500 LOC */